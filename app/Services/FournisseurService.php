<?php

namespace App\Services;

use App\Models\Achat;
use App\Models\Fournisseur;
use App\Models\Depense;
use App\Models\CategorieDepense;
use App\Models\ReglementFournisseur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FournisseurService
{
    public function __construct(protected StockService $stockService) {}

    /**
     * Valider un achat :
     * - Met à jour le stock via StockService
     * - Crée une dépense automatique
     * - Met à jour le solde dû du fournisseur (si crédit)
     */
    public function validerAchat(Achat $achat): Achat
    {
        return DB::transaction(function () use ($achat) {
            if ($achat->statut !== 'brouillon') {
                throw new \Exception("Cet achat est déjà validé.");
            }

            $achat->load('lignes.matierePremiere', 'fournisseur');

            // Calculer le montant total
            $montantTotal = $achat->lignes->sum(fn($l) => intval($l->quantite * $l->prix_unitaire));

            // Vérifier le plafond crédit si mode = credit
            if ($achat->mode_paiement === 'credit') {
                $fournisseur = $achat->fournisseur;
                if ($fournisseur->plafond_credit > 0 && !$fournisseur->peutAcheterACredit($montantTotal)) {
                    throw new \Exception(
                        "Plafond de crédit dépassé pour {$fournisseur->nom}. " .
                        "Disponible : " . number_format($fournisseur->credit_disponible) . " FCFA, " .
                        "Demandé : " . number_format($montantTotal) . " FCFA."
                    );
                }
            }

            // Mettre à jour le stock pour chaque ligne
            foreach ($achat->lignes as $ligne) {
                $this->stockService->addEntree(
                    $ligne->matierePremiere,
                    $ligne->quantite,
                    $ligne->prix_unitaire,
                    Achat::class,
                    $achat->id
                );
            }

            // Créer la dépense automatique
            $this->creerDepenseAchat($achat, $montantTotal);

            // Mettre à jour le solde fournisseur si crédit
            if ($achat->mode_paiement === 'credit') {
                $achat->fournisseur->increment('solde_du', $montantTotal);
            }

            // Déterminer le statut paiement
            $statutPaiement = match($achat->mode_paiement) {
                'credit' => 'valide',       // sera "partiellement_paye" ou "solde" après règlements
                default  => 'solde',        // paiement immédiat = soldé
            };

            $achat->update([
                'montant_total' => $montantTotal,
                'montant_paye'  => $achat->mode_paiement !== 'credit' ? $montantTotal : 0,
                'statut'        => $statutPaiement,
                'updated_by'    => Auth::id(),
            ]);

            return $achat->fresh();
        });
    }

    /**
     * Créer un règlement fournisseur :
     * - Enregistre le règlement
     * - Crée une dépense automatique
     * - Met à jour le solde dû du fournisseur
     * - Met à jour le statut de l'achat
     */
    public function creerReglement(array $data): ReglementFournisseur
    {
        return DB::transaction(function () use ($data) {
            $fournisseur = Fournisseur::findOrFail($data['fournisseur_id']);
            $montant     = intval($data['montant']);

            // Créer le règlement
            $reglement = ReglementFournisseur::create([
                'fournisseur_id'   => $fournisseur->id,
                'achat_id'         => $data['achat_id'] ?? null,
                'montant'          => $montant,
                'date_reglement'   => $data['date_reglement'],
                'mode_paiement'    => $data['mode_paiement'],
                'reference_mobile' => $data['reference_mobile'] ?? null,
                'reference_banque' => $data['reference_banque'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'created_by'       => Auth::id(),
            ]);

            // Mettre à jour solde fournisseur
            $fournisseur->decrement('solde_du', $montant);
            if ($fournisseur->solde_du < 0) {
                $fournisseur->update(['solde_du' => 0]);
            }

            // Mettre à jour le statut de l'achat lié
            if ($reglement->achat_id) {
                $achat = Achat::find($reglement->achat_id);
                if ($achat) {
                    $achat->increment('montant_paye', $montant);
                    $achat->refresh();
                    $statut = $achat->montant_paye >= $achat->montant_total ? 'solde' : 'partiellement_paye';
                    $achat->update(['statut' => $statut]);
                }
            }

            // Créer la dépense automatique
            $this->creerDepenseReglement($reglement, $fournisseur);

            return $reglement;
        });
    }

    /**
     * Créer une dépense automatique pour un achat validé
     */
    private function creerDepenseAchat(Achat $achat, int $montant): Depense
    {
        $categorie = $this->getCategorieAchats();

        return Depense::create([
            'categorie_depense_id' => $categorie->id,
            'fournisseur_id'       => $achat->fournisseur_id,
            'source_type'          => Achat::class,
            'source_id'            => $achat->id,
            'libelle'              => "Achat matières — {$achat->fournisseur->nom}" .
                                      ($achat->reference ? " (Réf: {$achat->reference})" : ''),
            'montant'              => $montant,
            'mode_paiement'        => $achat->mode_paiement === 'credit' ? 'autre' : $achat->mode_paiement,
            'date_depense'         => $achat->date_achat,
            'beneficiaire'         => $achat->fournisseur->nom,
            'statut'               => 'validee',
            'valide_par'           => Auth::id(),
            'valide_le'            => now(),
            'notes'                => $achat->mode_paiement === 'credit'
                                      ? "Achat à crédit — échéance : " . ($achat->date_echeance?->format('d/m/Y') ?? 'non définie')
                                      : null,
            'created_by'           => Auth::id(),
        ]);
    }

    /**
     * Créer une dépense automatique pour un règlement
     */
    private function creerDepenseReglement(ReglementFournisseur $reglement, Fournisseur $fournisseur): Depense
    {
        $categorie = $this->getCategorieReglements();

        return Depense::create([
            'categorie_depense_id' => $categorie->id,
            'fournisseur_id'       => $fournisseur->id,
            'source_type'          => ReglementFournisseur::class,
            'source_id'            => $reglement->id,
            'libelle'              => "Règlement fournisseur — {$fournisseur->nom}",
            'montant'              => $reglement->montant,
            'mode_paiement'        => $reglement->mode_paiement,
            'reference_mobile'     => $reglement->reference_mobile,
            'date_depense'         => $reglement->date_reglement,
            'beneficiaire'         => $fournisseur->nom,
            'statut'               => 'validee',
            'valide_par'           => Auth::id(),
            'valide_le'            => now(),
            'created_by'           => Auth::id(),
        ]);
    }

    /**
     * Obtenir ou créer la catégorie "Achats fournisseurs"
     */
    private function getCategorieAchats(): CategorieDepense
    {
        return CategorieDepense::firstOrCreate(
            ['nom' => 'Achats fournisseurs'],
            ['couleur' => '#E67E22']
        );
    }

    /**
     * Obtenir ou créer la catégorie "Règlements fournisseurs"
     */
    private function getCategorieReglements(): CategorieDepense
    {
        return CategorieDepense::firstOrCreate(
            ['nom' => 'Règlements fournisseurs'],
            ['couleur' => '#8E44AD']
        );
    }
}
