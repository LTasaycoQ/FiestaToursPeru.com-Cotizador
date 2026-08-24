<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('quote', function (Blueprint $table) {
            $table->id('id_quote');

            // Relaciones
            $table->foreignId('id_client')->nullable()->constrained('clients', 'id_client')->nullOnDelete();
            $table->foreignId('id_users')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->foreignId('id_contacts')->nullable()->constrained('contacts', 'id_contacts')->nullOnDelete();

            // Datos principales
            $table->string('name', 300)->comment('Nombre de la cotización');
            $table->string('quote_number', 50)->nullable()->comment('Número de cotización');

            // Correlativo / Número de File
            $table->string('correlative', 20)->nullable()->comment('Número de file: MM-NNN-AAAA (Ej: 01-001-2025)');

            // Fecha cuando se asignó el correlativo
            $table->timestamp('correlative_assigned_at')->nullable()
                ->comment('Fecha cuando se asignó el número de file');

            // Estado
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'expired', 'cancelled'])
                ->default('draft')
                ->comment('Estado de la cotización');

            // Fechas
            $table->integer('days')->nullable()->comment('Número de días');
            $table->date('start_date')->nullable()->comment('Fecha de inicio');
            $table->date('end_date')->nullable()->comment('Fecha de fin');
            $table->date('expiration_date')->nullable()->comment('Fecha de vencimiento');

            // Pasajeros
            $table->integer('passengers_count')->nullable()->comment('Cantidad de pasajeros');

            // Totales
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Observaciones
            $table->text('notes')->nullable()->comment('Observaciones');

            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index('status');
            $table->index('quote_number');
            $table->index('correlative');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote');
    }
};
