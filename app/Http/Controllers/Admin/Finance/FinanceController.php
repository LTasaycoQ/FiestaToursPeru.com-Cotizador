<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Exports\FinanceExport;
use App\Http\Controllers\Controller;
use App\Models\Finance\BalanceModel;
use App\Models\Finance\BalanceRecharges;
use App\Models\Finance\ProjectExpenseModel;
use App\Models\Finance\ProyectModel;
use App\Models\User;
use App\Notifications\LowBalanceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $projects = ProyectModel::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->with('balance')
            ->orderBy('id_proyect', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('finance.index', compact('projects', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|in:S/,$',
        ], [
            'name.required' => 'El nombre del proyecto es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'currency.required' => 'La moneda del proyecto es obligatoria.',
            'currency.in' => 'La moneda debe ser S/ o $.',
        ]);

        DB::beginTransaction();
        try {
            $balance = BalanceModel::create([
                'amount' => 0,
                'real_amount' => 0,
            ]);

            $project = ProyectModel::create([
                'name' => $request->name,
                'currency' => $request->currency,
                'id_balance' => $balance->id_balance,
            ]);

            DB::commit();

            return redirect()->route('finance.index')->with('success', 'Registrado Correctamente');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al crear el proyecto: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $project = ProyectModel::with([
                'balance',
                'expenses' => function ($query) {
                    $query->orderBy('id_expense', 'desc');
                },
            ])->findOrFail($id);

            $availableBalance = $project->balance ? $project->balance->amount : 0;
            $realBalance = $project->balance ? $project->balance->real_amount : 0;
            $currencySymbol = $project->currency_symbol;
            $totalExpenses = $project->expenses->sum('amount');

            // ── RECARGAS (paginadas) ──
            $recharges = $project->balance
                ? $project->balance->recharges()->orderBy('id_recharge', 'desc')->paginate(10)
                : collect();

            // ── GASTOS REGULARES (paginados) ──
            $regularExpenses = ProjectExpenseModel::where('id_proyect', $id)
                ->whereNotNull('file_number')
                ->where('file_number', '!=', '')
                ->orderBy('id_expense', 'desc')
                ->paginate(10);

            // ── GASTOS OPERATIVOS (paginados) ──
            $operationalExpenses = ProjectExpenseModel::where('id_proyect', $id)
                ->where(function ($query) {
                    $query->whereNull('file_number')
                        ->orWhere('file_number', '=', '');
                })
                ->orderBy('id_expense', 'desc')
                ->paginate(10);

            $totalOperationalExpenses = ProjectExpenseModel::where('id_proyect', $id)
                ->where(function ($query) {
                    $query->whereNull('file_number')
                        ->orWhere('file_number', '=', '');
                })
                ->sum('amount');

            $personTypes = ProjectExpenseModel::where('id_proyect', $id)
                ->whereNotNull('file_number')
                ->where('file_number', '!=', '')
                ->whereNotNull('name_people')
                ->where('name_people', '!=', '')
                ->select('name_people')
                ->distinct()
                ->pluck('name_people')
                ->values();

            return view('finance.show', compact(
                'project',
                'regularExpenses',
                'operationalExpenses',
                'totalExpenses',
                'totalOperationalExpenses',
                'availableBalance',
                'realBalance',
                'currencySymbol',
                'recharges',
                'personTypes'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el proyecto: '.$e->getMessage());
        }
    }

    public function registerExpense(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ], [
            'amount.required' => 'El monto del gasto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número válido.',
            'amount.min' => 'El monto mínimo es 0.01.',
        ]);

        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);

            if (! $project->balance) {
                return back()->with('error', 'El proyecto no tiene un balance asignado.');
            }

            $availableBalance = $project->balance->amount;

            if ($request->amount > $availableBalance) {
                return back()->with('error', 'No hay suficiente balance disponible. Balance actual: $'.number_format($availableBalance, 2));
            }

            $expense = ProjectExpenseModel::create([
                'id_proyect' => $id,
                'name_people' => $request->name_people ?? null,
                'amount' => $request->amount,
                'reservation_code' => $request->reservation_code ?? null,
                'file_number' => $request->file_number ?? null,
                'expense_date' => $request->expense_date ?? now(),
            ]);

            $project->balance->decrease($request->amount);

            $newAvailableBalance = $project->balance->amount;

            if ($newAvailableBalance <= 1000) {
                $this->sendLowBalanceNotification($project, $newAvailableBalance, $expense);
            }

            DB::commit();

            return redirect()->route('finance.show', $id)
                ->with('success', 'Gasto registrado correctamente. Balance actual: $'.number_format($newAvailableBalance, 2));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al registrar el gasto: '.$e->getMessage());
        }
    }

    public function updateRealBalance(Request $request, $id)
    {
        $request->validate([
            'real_amount' => 'required|numeric|min:0',
        ], [
            'real_amount.required' => 'El balance real del banco es obligatorio.',
            'real_amount.numeric' => 'El balance real debe ser un número válido.',
            'real_amount.min' => 'El balance real no puede ser menor a 0.',
        ]);

        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);

            if (! $project->balance) {
                return back()->with('error', 'El proyecto no tiene un balance asignado.');
            }

            $project->balance->update([
                'real_amount' => $request->real_amount,
            ]);

            DB::commit();

            return redirect()->route('finance.show', $id)
                ->with('success', 'Balance real en el banco actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al actualizar el balance real: '.$e->getMessage());
        }
    }

    public function rechargeBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ], [
            'amount.required' => 'El monto a recargar es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número válido.',
            'amount.min' => 'El monto mínimo es 0.01.',
        ]);

        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);

            if (! $project->balance) {
                return back()->with('error', 'El proyecto no tiene un balance asignado.');
            }

            $previousBalance = $project->balance->amount;
            $recharge = $project->balance->recharge($request->amount);

            DB::commit();

            return redirect()->route('finance.show', $id);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al recargar el balance: '.$e->getMessage());
        }
    }

    public function setInitialBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ], [
            'amount.required' => 'El monto inicial es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número válido.',
            'amount.min' => 'El monto mínimo es 0.01.',
        ]);

        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);

            if (! $project->balance) {
                return back()->with('error', 'El proyecto no tiene un balance asignado.');
            }

            if ($project->balance->amount > 0) {
                return back()->with('error', 'El proyecto ya tiene un balance asignado. Usa la opción de recarga.');
            }

            $project->balance->recharge($request->amount);

            if ($project->balance->real_amount === null) {
                $project->balance->update(['real_amount' => 0]);
            }

            DB::commit();

            return redirect()->route('finance.show', $id);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al asignar el balance inicial: '.$e->getMessage());
        }
    }

    private function sendLowBalanceNotification($project, $availableBalance, $expense)
    {
        try {
            $emails = [
                'dw@fiestatoursperu.com',
                'luistasayco3030@gmail.com',
            ];

            $users = User::whereIn('email', $emails)->get();

            if ($users->isEmpty()) {
                \Log::warning('No se encontraron usuarios con los correos configurados.');
                \Log::info('Correos buscados: '.implode(', ', $emails));

                return;
            }

            Notification::send($users, new LowBalanceNotification($project, $availableBalance, $expense));

            \Log::info('Notificación de balance bajo enviada a: '.implode(', ', $emails));

        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|in:S/,$',
        ], [
            'name.required' => 'El nombre del proyecto es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'currency.required' => 'La moneda del proyecto es obligatoria.',
            'currency.in' => 'La moneda debe ser S/ o $.',
        ]);

        $project = ProyectModel::findOrFail($id);
        $project->update([
            'name' => $request->name,
            'currency' => $request->currency,
        ]);

        return redirect()->route('finance.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy($id)
    {
        try {
            $project = ProyectModel::findOrFail($id);

            if ($project->balance) {
                $project->balance->delete();
            }

            $project->delete();

            return redirect()->route('finance.index')->with('success', 'Eliminado Correctamente');
        } catch (\Exception $e) {
            return redirect()->route('finance.index')
                ->with('error', 'Error al eliminar el proyecto: '.$e->getMessage());
        }
    }

    public function editExpense($id, $expenseId)
    {
        try {
            $project = ProyectModel::findOrFail($id);
            $expense = ProjectExpenseModel::findOrFail($expenseId);

            if ($expense->id_proyect != $id) {
                return response()->json(['error' => 'El gasto no pertenece a este proyecto'], 404);
            }

            return response()->json([
                'expense' => $expense,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateExpense(Request $request, $id, $expenseId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);
            $expense = ProjectExpenseModel::findOrFail($expenseId);

            if ($expense->id_proyect != $id) {
                return back()->with('error', 'El gasto no pertenece a este proyecto');
            }

            $balanceBeforeThisExpense = $project->balance->amount + $expense->amount;

            $newExpectedBalance = $balanceBeforeThisExpense - $request->amount;

            if ($newExpectedBalance < 0) {
                return back()->with('error', 'No hay suficiente balance para aumentar este gasto. Balance máximo permitido para este gasto: $'.number_format($balanceBeforeThisExpense, 2));
            }

            $expense->update([
                'name_people' => $request->name_people,
                'reservation_code' => $request->reservation_code,
                'amount' => $request->amount,
            ]);

            $project->balance->update([
                'amount' => $newExpectedBalance,
            ]);

            DB::commit();

            return redirect()->route('finance.show', $id)
                ->with('success', 'Gasto actualizado correctamente. Nuevo balance: $'.number_format($newExpectedBalance, 2));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al actualizar el gasto: '.$e->getMessage());
        }
    }

    public function destroyExpense($id, $expenseId)
    {
        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);
            $expense = ProjectExpenseModel::findOrFail($expenseId);

            if ($expense->id_proyect != $id) {
                return back()->with('error', 'El gasto no pertenece a este proyecto');
            }

            $project->balance->increase($expense->amount);

            $expense->delete();

            DB::commit();

            return redirect()->route('finance.show', $id)
                ->with('success', 'Gasto eliminado correctamente. Se devolvieron $'.number_format($expense->amount, 2).' al balance.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al eliminar el gasto: '.$e->getMessage());
        }
    }

    public function editRecharge($id, $rechargeId)
    {
        try {
            $project = ProyectModel::findOrFail($id);
            $recharge = BalanceRecharges::where('id_recharge', $rechargeId)
                ->where('id_balance', $project->id_balance)
                ->firstOrFail();

            return response()->json([
                'recharge' => $recharge,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateRecharge(Request $request, $id, $rechargeId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'recharge_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);
            $recharge = BalanceRecharges::where('id_recharge', $rechargeId)
                ->where('id_balance', $project->id_balance)
                ->firstOrFail();

            $balanceBeforeThisRecharge = $project->balance->amount - $recharge->amount;

            $newExpectedBalance = $balanceBeforeThisRecharge + $request->amount;

            if ($newExpectedBalance < 0) {
                return back()->with('error', 'La operación dejaría un balance negativo. Balance antes de la recarga: $'.number_format($balanceBeforeThisRecharge, 2));
            }

            $recharge->update([
                'amount' => $request->amount,
                'recharge_date' => $request->recharge_date,
            ]);

            $project->balance->update([
                'amount' => $newExpectedBalance,
            ]);

            DB::commit();

            return redirect()->route('finance.show', $id)
                ->with('success', 'Recarga actualizada correctamente. Nuevo balance: $'.number_format($newExpectedBalance, 2));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al actualizar la recarga: '.$e->getMessage());
        }
    }

    public function destroyRecharge($id, $rechargeId)
    {
        DB::beginTransaction();

        try {
            $project = ProyectModel::with('balance')->findOrFail($id);
            $recharge = BalanceRecharges::where('id_recharge', $rechargeId)
                ->where('id_balance', $project->id_balance)
                ->firstOrFail();

            $balanceBeforeThisRecharge = $project->balance->amount - $recharge->amount;

            $project->balance->update([
                'amount' => $balanceBeforeThisRecharge,
            ]);

            $recharge->delete();

            DB::commit();

            return redirect()->route('finance.show', $id)
                ->with('success', 'Recarga eliminada correctamente. Se actualizó el balance.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al eliminar la recarga: '.$e->getMessage());
        }
    }

    public function exportAll($id)
    {
        try {
            $project = ProyectModel::findOrFail($id);
            $filename = 'todos_los_gastos_'.str_replace(' ', '_', $project->name).'.xlsx';

            return Excel::download(new FinanceExport($id, 'all'), $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar gastos: '.$e->getMessage());
        }
    }

    public function exportRecharges($id)
    {
        try {
            $project = ProyectModel::findOrFail($id);
            $filename = 'recargas_'.str_replace(' ', '_', $project->name).'.xlsx';

            return Excel::download(new FinanceExport($id, 'recharges'), $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar recargas: '.$e->getMessage());
        }
    }
}
