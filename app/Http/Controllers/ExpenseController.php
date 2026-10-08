<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.1',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $register = \App\Models\CashRegister::where('status', 'open')->lockForUpdate()->first();
            if (! $register) {
                throw \Illuminate\Validation\ValidationException::withMessages(['cash_register' => 'Debe abrir una caja antes de registrar gastos.']);
            }
            Expense::create([
                'description' => $request->description, 'amount' => $request->amount,
                'user_id' => Auth::id(), 'cash_register_id' => $register->id,
            ]);
        });

        return redirect()->back()->with('success', 'Gasto registrado correctamente.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->back()->with('success', 'Gasto eliminado.');
    }
}
