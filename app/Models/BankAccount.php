<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $table = 'bank_account';
    protected $primaryKey = 'id_bank_account';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id_bank',
        'id_supplier',
        'account_number',
        'cci',
        'currency',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank', 'id_bank');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier')->withTrashed();
    }
}
