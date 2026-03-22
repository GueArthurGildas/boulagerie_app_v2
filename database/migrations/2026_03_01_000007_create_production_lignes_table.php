<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('production_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('restrict');
            $table->integer('quantite_produite')->default(0);
            $table->integer('quantite_invendue')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('production_lignes'); }
};
