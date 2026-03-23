<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchatLigne extends Model
{
    protected $fillable = [
        'achat_id', 'matiere_premiere_id', 'quantite', 'prix_unitaire',
    ];

    protected $casts = [
        'quantite'      => 'float',
        'prix_unitaire' => 'integer',
    ];

    public function achat()
    {
        return $this->belongsTo(Achat::class);
    }

    public function matierePremiere()
    {
        return $this->belongsTo(MatierePremiere::class);
    }

    public function getMontantAttribute(): int
    {
        return intval($this->quantite * $this->prix_unitaire);
    }
}
