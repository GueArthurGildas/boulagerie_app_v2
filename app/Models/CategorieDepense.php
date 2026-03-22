<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieDepense extends Model
{
    protected $table = 'categorie_depenses';

    protected $fillable = ['nom', 'couleur', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function depenses()
    {
        return $this->hasMany(Depense::class, 'categorie_depense_id');
    }
}
