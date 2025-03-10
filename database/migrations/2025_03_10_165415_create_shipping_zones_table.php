<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name')->unique();
            $table->decimal('additional_fee', 10, 2); // Phí vận chuyển theo khu vực
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipping_zones');
    }
};
