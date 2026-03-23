<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom', 'telephone', 'email', 'adresse', 'ville',
        'contact_nom', 'type', 'plafond_credit', 'solde_du',
        'actif', 'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'plafond_credit' => 'integer',
        'solde_du'       => 'integer',
        'actif'          => 'boolean',
    ];

    public function achats()
    {
        return $this->hasMany(Achat::class);
    }

    public function reglements()
    {
        return $this->hasMany(ReglementFournisseur::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    // Vérifie si un nouvel achat dépasse le plafond de crédit
    public function peutAcheterACredit(int $montant): bool
    {
        if ($this->plafond_credit <= 0) return false;
        return ($this->solde_du + $montant) <= $this->plafond_credit;
    }

    public function getCreditDisponibleAttribute(): int
    {
        if ($this->plafond_credit <= 0) return 0;
        return max(0, $this->plafond_credit - $this->solde_du);
    }

    public function getTauxEndettementAttribute(): float
    {
        if ($this->plafond_credit <= 0) return 0;
        return round(($this->solde_du / $this->plafond_credit) * 100, 1);
    }
}
