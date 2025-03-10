<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_cost', 10, 2);
            $table->decimal('cost_per_kg', 10, 2)->default(0);
            $table->integer('estimated_days')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipping_methods');
    }
};
