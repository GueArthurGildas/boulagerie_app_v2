<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatierePremiere extends Model {
    use SoftDeletes;
    protected $fillable = ['nom','unite','stock_actuel','stock_minimum','prix_moyen_pondere','date_peremption','actif','created_by','updated_by'];
    protected $casts = ['stock_actuel'=>'float','stock_minimum'=>'float','prix_moyen_pondere'=>'integer','date_peremption'=>'date','actif'=>'boolean'];
    public function recetteLignes() { return $this->hasMany(RecetteLigne::class); }
    public function stockMovements() { return $this->hasMany(StockMouvement::class); }
    public function isStockBas(): bool { return $this->stock_actuel <= $this->stock_minimum; }
    public function isPerime(): bool { return $this->date_peremption && $this->date_peremption->isPast(); }
    public function isPerimeSoon(): bool { return $this->date_peremption && !$this->date_peremption->isPast() && $this->date_peremption->diffInDays(now()) <= 3; }
}
