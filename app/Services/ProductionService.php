<?php

namespace App\Services;

use App\Models\Production;
use App\Models\Recette;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function __construct(protected StockService $stockService) {}

    /**
     * Vérifier si le stock est suffisant pour une recette
     */
    public function verifierDisponibilite(Recette $recette): array
    {
        $manquants = [];
        foreach ($recette->lignes as $ligne) {
            $m = $ligne->matierePremiere;
            if ($m->stock_actuel < $ligne->quantite) {
                $manquants[] = [
                    'matiere'    => $m->nom,
                    'unite'      => $m->unite,
                    'requis'     => $ligne->quantite,
                    'disponible' => $m->stock_actuel,
                    'manque'     => round($ligne->quantite - $m->stock_actuel, 3),
                ];
            }
        }
        return ['ok' => empty($manquants), 'manquants' => $manquants];
    }

    /**
     * Démarrer une fournée : vérifie le stock et consomme les matières
     */
    public function startFournee(int $recetteId, string $equipe = 'jour', ?string $notes = null): Production
    {
        return DB::transaction(function () use ($recetteId, $equipe, $notes) {
            $recette = Recette::with('lignes.matierePremiere')->findOrFail($recetteId);

            $dispo = $this->verifierDisponibilite($recette);
            if (!$dispo['ok']) {
                $details = collect($dispo['manquants'])
                    ->map(fn($m) => "{$m['matiere']} : manque {$m['manque']} {$m['unite']}")
                    ->join(', ');
                throw new \Exception("Stock insuffisant : {$details}");
            }

            $production = Production::create([
                'recette_id'          => $recette->id,
                'date_production'     => now()->toDateString(),
                'equipe'              => $equipe,
                'statut'              => 'en_cours',
                'nb_pieces_attendues' => $recette->nb_pieces_attendues,
                'notes'               => $notes,
                'created_by'          => Auth::id(),
            ]);

            foreach ($recette->lignes as $ligne) {
                $this->stockService->addSortie(
                    $ligne->matierePremiere,
                    $ligne->quantite,
                    Production::class,
                    $production->id
                );
            }

            return $production;
        });
    }

    /**
     * Clôturer une fournée en cours
     */
    public function closeFournee(Production $production, array $lignes): Production
    {
        return DB::transaction(function () use ($production, $lignes) {
            if ($production->statut !== 'en_cours') {
                throw new \Exception("Cette fournée ne peut plus être clôturée (statut : {$production->statut}).");
            }

            $totalProduit = 0;
            $totalInvendu = 0;

            foreach ($lignes as $ligne) {
                $qteInvendue = $ligne['quantite_invendue'] ?? 0;
                $production->lignes()->updateOrCreate(
                    ['produit_id' => $ligne['produit_id']],
                    [
                        'quantite_produite' => $ligne['quantite_produite'],
                        'quantite_invendue' => $qteInvendue,
                    ]
                );
                $totalProduit += $ligne['quantite_produite'];
                $totalInvendu += $qteInvendue;
            }

            $rendement = $production->nb_pieces_attendues > 0
                ? round(($totalProduit / $production->nb_pieces_attendues) * 100, 2)
                : 0;

            $production->update([
                'nb_pieces_produites' => $totalProduit,
                'nb_pieces_invendues' => $totalInvendu,
                'rendement'           => $rendement,
                'statut'              => 'terminee',
                'updated_by'          => Auth::id(),
            ]);

            return $production->fresh();
        });
    }

    /**
     * Corriger les quantités produites d'une fournée terminée
     * Note : ne modifie PAS le stock (correction comptable uniquement)
     */
    public function correctFournee(Production $production, array $lignes, string $motif): Production
    {
        return DB::transaction(function () use ($production, $lignes, $motif) {
            if ($production->statut !== 'terminee') {
                throw new \Exception("Seules les fournées terminées peuvent être corrigées.");
            }

            $totalProduit = 0;
            $totalInvendu = 0;

            foreach ($lignes as $ligne) {
                $qteInvendue = $ligne['quantite_invendue'] ?? 0;
                $production->lignes()->updateOrCreate(
                    ['produit_id' => $ligne['produit_id']],
                    [
                        'quantite_produite' => $ligne['quantite_produite'],
                        'quantite_invendue' => $qteInvendue,
                    ]
                );
                $totalProduit += $ligne['quantite_produite'];
                $totalInvendu += $qteInvendue;
            }

            $rendement = $production->nb_pieces_attendues > 0
                ? round(($totalProduit / $production->nb_pieces_attendues) * 100, 2)
                : 0;

            $production->update([
                'nb_pieces_produites' => $totalProduit,
                'nb_pieces_invendues' => $totalInvendu,
                'rendement'           => $rendement,
                'notes'               => ($production->notes ? $production->notes . "\n" : '')
                                        . '[Correction ' . now()->format('d/m/Y H:i') . '] ' . $motif,
                'updated_by'          => Auth::id(),
            ]);

            return $production->fresh();
        });
    }

    /**
     * Mettre à jour les invendus uniquement
     */
    public function updateInvendus(Production $production, array $lignes): Production
    {
        return DB::transaction(function () use ($production, $lignes) {
            if (!in_array($production->statut, ['terminee'])) {
                throw new \Exception("Les invendus ne peuvent être modifiés que sur une fournée terminée.");
            }

            $totalInvendu = 0;

            foreach ($lignes as $ligne) {
                $ligneModel = $production->lignes()->findOrFail($ligne['ligne_id']);

                // Validation : les invendus ne peuvent pas dépasser le produit
                if ($ligne['quantite_invendue'] > $ligneModel->quantite_produite) {
                    throw new \Exception(
                        "Les invendus ({$ligne['quantite_invendue']}) ne peuvent pas dépasser "
                        . "les quantités produites ({$ligneModel->quantite_produite}) pour {$ligneModel->produit->nom}."
                    );
                }

                $ligneModel->update(['quantite_invendue' => $ligne['quantite_invendue']]);
                $totalInvendu += $ligne['quantite_invendue'];
            }

            // Recalcul total invendus
            $production->update([
                'nb_pieces_invendues' => $production->lignes()->sum('quantite_invendue'),
                'updated_by'          => Auth::id(),
            ]);

            return $production->fresh();
        });
    }

    /**
     * Annuler une fournée et restituer les matières au stock
     */
    public function annulerFournee(Production $production, string $motif): Production
    {
        return DB::transaction(function () use ($production, $motif) {
            if ($production->statut === 'annulee') {
                throw new \Exception("Cette fournée est déjà annulée.");
            }

            if ($production->statut === 'terminee') {
                // Avertissement dans les notes mais on autorise
                // Dans un contexte réel on pourrait bloquer si des ventes ont été faites
            }

            // Restituer les matières au stock
            $recette = $production->recette()->with('lignes.matierePremiere')->first();

            foreach ($recette->lignes as $ligne) {
                $this->stockService->addEntree(
                    $ligne->matierePremiere,
                    $ligne->quantite,
                    $ligne->matierePremiere->prix_moyen_pondere,
                    Production::class,
                    $production->id
                );
            }

            $production->update([
                'statut'     => 'annulee',
                'notes'      => ($production->notes ? $production->notes . "\n" : '')
                                . '[Annulation ' . now()->format('d/m/Y H:i') . '] ' . $motif,
                'updated_by' => Auth::id(),
            ]);

            return $production->fresh();
        });
    }
}
