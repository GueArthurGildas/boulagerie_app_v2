<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('incident_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
            $table->string('type_incident');
            $table->text('description')->nullable();
            $table->integer('duree_arret_minutes')->default(0);
            $table->decimal('impact_fcfa', 14, 0)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('incident_productions'); }
};
