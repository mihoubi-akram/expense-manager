<?php

namespace App\Policies;

use App\Enums\ExpenseStatus;
use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->isManager() || $user->id === $expense->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isEmployee();
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->id !== $expense->user_id) {
            return false;
        }

        return in_array($expense->status, [ExpenseStatus::DRAFT, ExpenseStatus::REJECTED]);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->isManager();
    }

    public function submit(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id &&
               in_array($expense->status, [ExpenseStatus::DRAFT, ExpenseStatus::REJECTED]);
    }

    public function approve(User $user, Expense $expense): bool
    {
        return $user->isManager() &&
               $expense->status === ExpenseStatus::SUBMITTED;
    }

    public function reject(User $user, Expense $expense): bool
    {
        return $user->isManager() &&
               $expense->status === ExpenseStatus::SUBMITTED;
    }

    public function pay(User $user, Expense $expense): bool
    {
        return $user->isManager() &&
               $expense->status === ExpenseStatus::APPROVED;
    }

    public function restore(User $user, Expense $expense): bool
    {
        return $user->isManager();
    }

    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->isManager();
    }
}
