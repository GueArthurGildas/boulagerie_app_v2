<?php
namespace App\Services;
use App\Models\Production;
use App\Models\Recette;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionService {
    public function __construct(protected StockService $stockService) {}

    public function verifierDisponibilite(Recette $recette): array {
        $manquants = [];
        foreach ($recette->lignes as $ligne) {
            $m = $ligne->matierePremiere;
            if ($m->stock_actuel < $ligne->quantite) $manquants[] = ['matiere'=>$m->nom,'unite'=>$m->unite,'requis'=>$ligne->quantite,'disponible'=>$m->stock_actuel,'manque'=>$ligne->quantite - $m->stock_actuel];
        }
        return ['ok'=>empty($manquants),'manquants'=>$manquants];
    }

    public function startFournee(int $recetteId, string $equipe = 'jour', string $notes = null): Production {
        return DB::transaction(function () use ($recetteId, $equipe, $notes) {
            $recette = Recette::with('lignes.matierePremiere')->findOrFail($recetteId);
            $dispo = $this->verifierDisponibilite($recette);
            if (!$dispo['ok']) {
                $details = collect($dispo['manquants'])->map(fn($m)=>"{$m['matiere']} : manque {$m['manque']} {$m['unite']}")->join(', ');
                throw new \Exception("Stock insuffisant : {$details}");
            }
            $production = Production::create(['recette_id'=>$recette->id,'date_production'=>now()->toDateString(),'equipe'=>$equipe,'statut'=>'en_cours','nb_pieces_attendues'=>$recette->nb_pieces_attendues,'notes'=>$notes,'created_by'=>Auth::id()]);
            foreach ($recette->lignes as $ligne) $this->stockService->addSortie($ligne->matierePremiere, $ligne->quantite, Production::class, $production->id);
            return $production;
        });
    }

    public function closeFournee(Production $production, array $lignes): Production {
        return DB::transaction(function () use ($production, $lignes) {
            if ($production->statut !== 'en_cours') throw new \Exception("Cette fournée ne peut plus être modifiée.");
            $totalProduit = 0; $totalInvendu = 0;
            foreach ($lignes as $ligne) {
                $production->lignes()->updateOrCreate(['produit_id'=>$ligne['produit_id']],['quantite_produite'=>$ligne['quantite_produite'],'quantite_invendue'=>$ligne['quantite_invendue'] ?? 0]);
                $totalProduit += $ligne['quantite_produite'];
                $totalInvendu += $ligne['quantite_invendue'] ?? 0;
            }
            $rendement = $production->nb_pieces_attendues > 0 ? round(($totalProduit / $production->nb_pieces_attendues) * 100, 2) : 0;
            $production->update(['nb_pieces_produites'=>$totalProduit,'nb_pieces_invendues'=>$totalInvendu,'rendement'=>$rendement,'statut'=>'terminee','updated_by'=>Auth::id()]);
            return $production->fresh();
        });
    }
}
