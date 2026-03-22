<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recette_id')->constrained('recettes')->onDelete('restrict');
            $table->date('date_production');
            $table->enum('equipe', ['jour','nuit'])->default('jour');
            $table->enum('statut', ['en_cours','terminee','annulee'])->default('en_cours');
            $table->integer('nb_pieces_attendues')->default(0);
            $table->integer('nb_pieces_produites')->default(0);
            $table->integer('nb_pieces_invendues')->default(0);
            $table->decimal('rendement', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('productions'); }
};
