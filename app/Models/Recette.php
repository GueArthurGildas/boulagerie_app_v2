<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Recette extends Model {
    use SoftDeletes;
    protected $fillable = ['nom','description','nb_pieces_attendues','actif','created_by','updated_by'];
    protected $casts = ['actif'=>'boolean'];
    public function lignes() { return $this->hasMany(RecetteLigne::class); }
    public function productions() { return $this->hasMany(Production::class); }
}
