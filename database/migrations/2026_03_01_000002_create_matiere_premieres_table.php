<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('matiere_premieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('unite');
            $table->decimal('stock_actuel', 14, 3)->default(0);
            $table->decimal('stock_minimum', 14, 3)->default(0);
            $table->decimal('prix_moyen_pondere', 14, 0)->default(0);
            $table->date('date_peremption')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('matiere_premieres'); }
};
