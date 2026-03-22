<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockMouvement extends Model {
    protected $fillable = ['matiere_premiere_id','type','quantite','prix_unitaire','reference_type','reference_id','motif','stock_avant','stock_apres','created_by'];
    protected $casts = ['quantite'=>'float','prix_unitaire'=>'integer','stock_avant'=>'float','stock_apres'=>'float'];
    public function matierePremiere() { return $this->belongsTo(MatierePremiere::class); }
}
