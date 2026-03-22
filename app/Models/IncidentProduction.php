<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class IncidentProduction extends Model {
    protected $fillable = ['production_id','type_incident','description','duree_arret_minutes','impact_fcfa','created_by'];
    protected $casts = ['impact_fcfa'=>'integer'];
    public function production() { return $this->belongsTo(Production::class); }
    public function createdBy() { return $this->belongsTo(User::class,'created_by'); }
}
