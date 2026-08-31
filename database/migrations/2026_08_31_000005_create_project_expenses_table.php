<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

        protected $connection = 'budget_manager';

    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id('id_expense');
            $table->unsignedBigInteger('id_proyect');
            $table->decimal('amount', 10, 2);
            $table->timestamp('expense_date');
            $table->string('reservation_code', 255)->nullable();
            $table->string('name_people', 255)->nullable();
            $table->string('file_number', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_proyect')
                  ->references('id_proyect')
                  ->on('proyect')
                  ->onDelete('cascade');


            $table->index('id_proyect');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};