<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    protected $connection = 'budget_manager';

    public function up(): void
    {
         Schema::create('balance_recharges', function (Blueprint $table) {
            $table->id('id_recharge');
            $table->decimal('amount', 12, 2);
            $table->decimal('previous_balance', 12, 2);
            $table->decimal('new_balance', 12, 2);
            $table->timestamp('recharge_date')->useCurrent();
            $table->timestamps();
            
             $table->foreignId('id_balance')
                ->nullable()
                ->constrained('balance', 'id_balance')
                ->cascadeOnDelete();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_recharges');
    }
};
