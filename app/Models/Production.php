<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Production extends Model {
    use SoftDeletes;
    protected $fillable = ['recette_id','date_production','equipe','statut','nb_pieces_attendues','nb_pieces_produites','nb_pieces_invendues','rendement','notes','created_by','updated_by'];
    protected $casts = ['date_production'=>'date','rendement'=>'float'];
    public function recette() { return $this->belongsTo(Recette::class); }
    public function lignes() { return $this->hasMany(ProductionLigne::class); }
    public function incidents() { return $this->hasMany(IncidentProduction::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
