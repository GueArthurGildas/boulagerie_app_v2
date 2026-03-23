<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglementFournisseur extends Model
{
    protected $fillable = [
        'fournisseur_id', 'achat_id', 'montant', 'date_reglement',
        'mode_paiement', 'reference_mobile', 'reference_banque',
        'notes', 'created_by',
    ];

    protected $casts = [
        'montant'         => 'integer',
        'date_reglement'  => 'date',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function achat()
    {
        return $this->belongsTo(Achat::class);
    }

    public function depense()
    {
        return $this->hasOne(Depense::class, 'source_id')
                    ->where('source_type', ReglementFournisseur::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getModeLibelleAttribute(): string
    {
        return match($this->mode_paiement) {
            'cash'         => 'Cash',
            'orange_money' => 'Orange Money',
            'wave'         => 'Wave',
            'mtn_momo'     => 'MTN MoMo',
            'banque'       => 'Banque',
            default        => 'Autre',
        };
    }
}
