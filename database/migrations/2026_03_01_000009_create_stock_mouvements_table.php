<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('stock_mouvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_premiere_id')->constrained('matiere_premieres')->onDelete('restrict');
            $table->enum('type', ['entree','sortie','inventaire','ajustement']);
            $table->decimal('quantite', 14, 3);
            $table->decimal('prix_unitaire', 14, 0)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('motif')->nullable();
            $table->decimal('stock_avant', 14, 3)->default(0);
            $table->decimal('stock_apres', 14, 3)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_mouvements'); }
};
