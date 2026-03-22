<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categorie_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('couleur', 7)->default('#888888'); // hex color
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_depense_id')->constrained('categorie_depenses')->onDelete('restrict');
            $table->string('libelle');
            $table->decimal('montant', 14, 0);
            $table->enum('mode_paiement', ['cash', 'orange_money', 'wave', 'mtn_momo', 'banque', 'autre'])
                  ->default('cash');
            $table->string('reference_mobile', 20)->nullable(); // ref transaction mobile money
            $table->date('date_depense');
            $table->string('beneficiaire')->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['brouillon', 'validee', 'rejetee'])->default('validee');
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->boolean('est_recurrente')->default(false);
            $table->enum('frequence_recurrence', ['hebdomadaire', 'mensuelle', 'trimestrielle', 'annuelle'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('categorie_depenses');
    }
};
