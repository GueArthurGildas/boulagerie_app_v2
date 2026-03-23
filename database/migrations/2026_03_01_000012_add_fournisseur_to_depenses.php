<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->unsignedBigInteger('fournisseur_id')->nullable()->after('categorie_depense_id');
            $table->string('source_type')->nullable()->after('fournisseur_id'); // App\Models\Achat, App\Models\ReglementFournisseur
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');

            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropColumn(['fournisseur_id', 'source_type', 'source_id']);
        });
    }
};
