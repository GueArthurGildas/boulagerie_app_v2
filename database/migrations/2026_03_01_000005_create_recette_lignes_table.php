<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('recette_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recette_id')->constrained('recettes')->onDelete('cascade');
            $table->foreignId('matiere_premiere_id')->constrained('matiere_premieres')->onDelete('restrict');
            $table->decimal('quantite', 14, 3);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('recette_lignes'); }
};
