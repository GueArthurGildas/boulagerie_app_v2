<?php
namespace App\Services;
use App\Models\MatierePremiere;
use App\Models\StockMouvement;
use Illuminate\Support\Facades\Auth;

class StockService {
    public function addEntree(MatierePremiere $matiere, float $quantite, int $prixUnitaire, string $referenceType = null, int $referenceId = null): StockMouvement {
        $stockAvant = $matiere->stock_actuel;
        $this->recalculatePmp($matiere, $quantite, $prixUnitaire);
        $matiere->stock_actuel += $quantite;
        $matiere->save();
        $mouvement = StockMouvement::create(['matiere_premiere_id'=>$matiere->id,'type'=>'entree','quantite'=>$quantite,'prix_unitaire'=>$prixUnitaire,'reference_type'=>$referenceType,'reference_id'=>$referenceId,'stock_avant'=>$stockAvant,'stock_apres'=>$matiere->stock_actuel,'created_by'=>Auth::id()]);
        $this->checkAlerte($matiere);
        return $mouvement;
    }
    public function addSortie(MatierePremiere $matiere, float $quantite, string $referenceType = null, int $referenceId = null): StockMouvement {
        if ($matiere->stock_actuel < $quantite) throw new \Exception("Stock insuffisant pour {$matiere->nom}. Disponible : {$matiere->stock_actuel} {$matiere->unite}");
        $stockAvant = $matiere->stock_actuel;
        $matiere->stock_actuel -= $quantite;
        $matiere->save();
        $mouvement = StockMouvement::create(['matiere_premiere_id'=>$matiere->id,'type'=>'sortie','quantite'=>$quantite,'prix_unitaire'=>$matiere->prix_moyen_pondere,'reference_type'=>$referenceType,'reference_id'=>$referenceId,'stock_avant'=>$stockAvant,'stock_apres'=>$matiere->stock_actuel,'created_by'=>Auth::id()]);
        $this->checkAlerte($matiere);
        return $mouvement;
    }
    public function recalculatePmp(MatierePremiere $matiere, float $nouvelleQte, int $nouveauPrix): void {
        $s = $matiere->stock_actuel; $p = $matiere->prix_moyen_pondere;
        if (($s + $nouvelleQte) > 0) $matiere->prix_moyen_pondere = (int) round((($s * $p) + ($nouvelleQte * $nouveauPrix)) / ($s + $nouvelleQte));
    }
    public function checkAlerte(MatierePremiere $matiere): void {
        if ($matiere->stock_actuel <= $matiere->stock_minimum) session()->flash('alerte_stock', "⚠️ Stock bas : {$matiere->nom} ({$matiere->stock_actuel} {$matiere->unite})");
    }
}
