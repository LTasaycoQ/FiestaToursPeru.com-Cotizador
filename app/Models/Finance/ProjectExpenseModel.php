<?php
// app/Models/Finance/ProjectExpenseModel.php
namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class ProjectExpenseModel extends Model
{
    protected $connection = 'budget_manager';

    protected $table = 'project_expenses';
    protected $primaryKey = 'id_expense';

    protected $fillable = [
        'id_proyect','name_people', 'amount',  'expense_date', 'reservation_code', 'file_number'
    ];

    protected $casts = [
        'expense_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(ProyectModel::class, 'id_proyect', 'id_proyect');
    }

   
}