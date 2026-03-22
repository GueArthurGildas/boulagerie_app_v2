<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RecetteLigne extends Model {
    protected $fillable = ['recette_id','matiere_premiere_id','quantite'];
    protected $casts = ['quantite'=>'float'];
    public function recette() { return $this->belongsTo(Recette::class); }
    public function matierePremiere() { return $this->belongsTo(MatierePremiere::class); }
}
