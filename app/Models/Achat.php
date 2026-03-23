<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fournisseur_id', 'reference', 'date_achat', 'date_echeance',
        'montant_total', 'montant_paye', 'statut', 'mode_paiement',
        'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'date_achat'    => 'date',
        'date_echeance' => 'date',
        'montant_total' => 'integer',
        'montant_paye'  => 'integer',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function lignes()
    {
        return $this->hasMany(AchatLigne::class);
    }

    public function reglements()
    {
        return $this->hasMany(ReglementFournisseur::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class, 'source_id')
                    ->where('source_type', Achat::class);
    }

    public function getMontantResteAttribute(): int
    {
        return max(0, $this->montant_total - $this->montant_paye);
    }

    public function isEcheanceDepassee(): bool
    {
        return $this->date_echeance && $this->date_echeance->isPast()
               && $this->statut !== 'solde';
    }
}
