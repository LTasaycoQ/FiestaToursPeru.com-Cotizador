<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'id_bank' => 'required|exists:bank,id_bank',
            'account_number' => 'required|string|max:100',
            'cci' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:40',
        ]);

        $account = BankAccount::create([
            'id_supplier' => $request->id_supplier,
            'id_bank' => $request->id_bank,
            'account_number' => $request->account_number,
            'cci' => $request->cci,
            'currency' => $request->currency,
        ]);

        return response()->json([
            'success' => true,
            'account' => $account
        ]);
    }

    /**
     * Obtener datos de una cuenta bancaria para edición
     */
    public function editData($id)
    {
        $account = BankAccount::with('bank')->findOrFail($id);

        return response()->json([
            'success' => true,
            'account' => $account
        ]);
    }

    /**
     * Actualizar una cuenta bancaria
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_bank' => 'required|exists:bank,id_bank',
            'account_number' => 'required|string|max:100',
            'cci' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:40',
        ]);

        $account = BankAccount::findOrFail($id);
        $account->update([
            'id_bank' => $request->id_bank,
            'account_number' => $request->account_number,
            'cci' => $request->cci,
            'currency' => $request->currency,
        ]);

        return response()->json([
            'success' => true,
            'account' => $account
        ]);
    }

    public function destroy($id)
    {
        $account = BankAccount::findOrFail($id);
        $account->delete();

        return redirect()->back()->with('success', 'Cuenta bancaria eliminada correctamente.');
    }
}
