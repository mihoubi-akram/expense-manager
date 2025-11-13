<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\ExpenseLog;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        ExpenseLog::create([
            'expense_id' => $expense->id,
            'user_id' => $expense->user_id,
            'from_status' => null,
            'to_status' => $expense->status->value,
        ]);
    }

    public function updated(Expense $expense): void
    {
        if ($expense->wasChanged('status')) {
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => auth()->id(),
                'from_status' => $expense->getOriginal('status'),
                'to_status' => $expense->status->value,
            ]);
        }
    }
}
