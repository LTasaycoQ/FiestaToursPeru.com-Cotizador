<?php
// app/Models/Finance/BalanceModel.php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BalanceModel extends Model
{
    protected $connection = 'budget_manager';
    protected $table = 'balance';
    protected $primaryKey = 'id_balance';

    protected $fillable = ['amount', 'real_amount'];

    protected $casts = [
        'amount' => 'decimal:2',
        'real_amount' => 'decimal:2',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(ProyectModel::class, 'id_balance', 'id_balance');
    }

   
    public function recharges(): HasMany
    {
        return $this->hasMany(BalanceRecharges::class, 'id_balance', 'id_balance');
    }

    public function decrease($amount): self
    {
        $this->amount -= $amount;
        $this->save();
        
        return $this;
    }

    public function increase($amount): self
    {
        $this->amount += $amount;
        $this->save();
        
        return $this;
    }

    public function recharge($amount): BalanceRecharges
    {
        $previousBalance = $this->amount;
        $newBalance = $previousBalance + $amount;

        $recharge = $this->recharges()->create([
            'amount' => $amount,
            'previous_balance' => $previousBalance,
            'new_balance' => $newBalance,
            'recharge_date' => now()
        ]);

        $this->increase($amount);

        return $recharge;
    }
}