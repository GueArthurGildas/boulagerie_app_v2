<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionLigne extends Model {
    protected $fillable = ['production_id','produit_id','quantite_produite','quantite_invendue'];
    public function production() { return $this->belongsTo(Production::class); }
    public function produit() { return $this->belongsTo(Produit::class); }
}
