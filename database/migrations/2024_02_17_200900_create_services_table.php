<?php

use App\Enums\ServiceIdentifier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('identifier', [
                ServiceIdentifier::Backoffice->value,
                ServiceIdentifier::Storage->value,
                ServiceIdentifier::Proxy->value,
                ServiceIdentifier::Speech->value,
            ]);
            $table->text('memo');
            $table->decimal('unit_price', 8, 5);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
