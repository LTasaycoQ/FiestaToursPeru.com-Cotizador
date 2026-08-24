<?php
namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class ProyectModel extends Model
{
    protected $connection = 'budget_manager';
    protected $table = 'proyect';
    protected $primaryKey = 'id_proyect';

    protected $fillable = ['id_balance', 'name', 'created_at', 'updated_at'];

    public function balance()
    {
        return $this->belongsTo(BalanceModel::class, 'id_balance', 'id_balance');
    }

   

    public function expenses()
    {
        return $this->hasMany(ProjectExpenseModel::class, 'id_proyect', 'id_proyect');
    }
}