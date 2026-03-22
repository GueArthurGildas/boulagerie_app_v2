<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Produit extends Model {
    use SoftDeletes;
    protected $fillable = ['nom','categorie','prix_vente','actif','created_by','updated_by'];
    protected $casts = ['prix_vente'=>'integer','actif'=>'boolean'];
    public function productionLignes() { return $this->hasMany(ProductionLigne::class); }
}
