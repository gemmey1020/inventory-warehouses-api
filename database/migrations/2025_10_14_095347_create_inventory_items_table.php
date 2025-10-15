<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['name', 'price']); // index for faster search by name and price
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
