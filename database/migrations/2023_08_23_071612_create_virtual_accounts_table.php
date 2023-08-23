<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('bank', 100);
            $table->string('va_number', 100);
            $table->unsignedInteger('wallet_id');
            $table->foreign('wallet_id')->on('wallets')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('virtual_accounts');
    }
};
