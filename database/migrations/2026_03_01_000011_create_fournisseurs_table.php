<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Fournisseurs ─────────────────────────────────────────
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('contact_nom')->nullable();       // nom du contact principal
            $table->string('type')->default('general');      // general, matiere, emballage...
            $table->decimal('plafond_credit', 14, 0)->default(0); // 0 = pas de crédit
            $table->decimal('solde_du', 14, 0)->default(0); // montant total dû
            $table->boolean('actif')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Achats (bons de livraison / factures) ─────────────────
        Schema::create('achats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('restrict');
            $table->string('reference')->nullable();         // N° BL ou facture
            $table->date('date_achat');
            $table->date('date_echeance')->nullable();       // date limite de paiement
            $table->decimal('montant_total', 14, 0)->default(0);
            $table->decimal('montant_paye', 14, 0)->default(0);
            $table->enum('statut', ['brouillon', 'valide', 'partiellement_paye', 'solde'])->default('brouillon');
            $table->enum('mode_paiement', ['cash', 'orange_money', 'wave', 'mtn_momo', 'banque', 'credit', 'autre'])->default('cash');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Lignes d'achat ────────────────────────────────────────
        Schema::create('achat_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achat_id')->constrained('achats')->onDelete('cascade');
            $table->foreignId('matiere_premiere_id')->constrained('matiere_premieres')->onDelete('restrict');
            $table->decimal('quantite', 14, 3);
            $table->decimal('prix_unitaire', 14, 0);
            $table->decimal('montant', 14, 0)->storedAs('quantite * prix_unitaire');
            $table->timestamps();
        });

        // ── Règlements fournisseurs ───────────────────────────────
        Schema::create('reglement_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('restrict');
            $table->foreignId('achat_id')->nullable()->constrained('achats')->onDelete('set null');
            $table->decimal('montant', 14, 0);
            $table->date('date_reglement');
            $table->enum('mode_paiement', ['cash', 'orange_money', 'wave', 'mtn_momo', 'banque', 'autre'])->default('cash');
            $table->string('reference_mobile', 20)->nullable();
            $table->string('reference_banque')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglement_fournisseurs');
        Schema::dropIfExists('achat_lignes');
        Schema::dropIfExists('achats');
        Schema::dropIfExists('fournisseurs');
    }
};
