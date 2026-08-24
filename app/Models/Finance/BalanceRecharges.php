<?php
// app/Models/Finance/BalanceRecharges.php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceRecharges extends Model
{
    protected $connection = 'budget_manager';
    protected $table = 'balance_recharges';
    protected $primaryKey = 'id_recharge';

    protected $fillable = [
        'id_balance',
        'amount',
        'previous_balance',
        'new_balance',
        'recharge_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
        'recharge_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el balance
     */
    public function balance(): BelongsTo
    {
        return $this->belongsTo(BalanceModel::class, 'id_balance', 'id_balance');
    }
}