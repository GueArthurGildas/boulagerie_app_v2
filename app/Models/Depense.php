<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'categorie_depense_id', 'fournisseur_id', 'source_type', 'source_id',
        'libelle', 'montant', 'mode_paiement', 'reference_mobile',
        'date_depense', 'beneficiaire', 'notes', 'statut',
        'valide_par', 'valide_le', 'est_recurrente', 'frequence_recurrence',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'montant'        => 'integer',
        'date_depense'   => 'date',
        'valide_le'      => 'datetime',
        'est_recurrente' => 'boolean',
    ];

    public function categorie()
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_depense_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function source()
    {
        return $this->morphTo('source');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
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

    public function scopeValidees($q)   { return $q->where('statut', 'validee'); }
    public function scopeBrouillons($q) { return $q->where('statut', 'brouillon'); }
    public function scopeRejetees($q)   { return $q->where('statut', 'rejetee'); }
}
