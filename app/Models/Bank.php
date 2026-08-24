<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $table      = 'bank';
    protected $primaryKey = 'id_bank';
    protected $dates = ['deleted_at'];

    protected $fillable   = [
        'bank_name',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'id_bank', 'id_bank')->withTrashed();
    }
}
