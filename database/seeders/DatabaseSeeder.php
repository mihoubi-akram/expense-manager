<?php

namespace Database\Seeders;

use App\Enums\ExpenseStatus;
use App\Enums\Role;
use App\Models\Comment;
use App\Models\Expense;
use App\Models\ExpenseLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer 1 manager
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'role' => Role::MANAGER,
        ]);

        // Créer 2 employés
        $employee1 = User::factory()->create([
            'name' => 'Employee One',
            'email' => 'employee1@example.com',
            'password' => bcrypt('password'),
            'role' => Role::EMPLOYEE,
        ]);

        $employee2 = User::factory()->create([
            'name' => 'Employee Two',
            'email' => 'employee2@example.com',
            'password' => bcrypt('password'),
            'role' => Role::EMPLOYEE,
        ]);

        // Créer 10 notes de frais avec différents statuts

        // 2 DRAFT pour employee1
        Expense::factory()->count(2)->create([
            'user_id' => $employee1->id,
            'status' => ExpenseStatus::DRAFT,
        ]);

        // 2 SUBMITTED pour employee1
        $submittedExpenses = Expense::factory()->count(2)->create([
            'user_id' => $employee1->id,
            'status' => ExpenseStatus::SUBMITTED,
        ]);
        // Créer des logs pour ces dépenses
        foreach ($submittedExpenses as $expense) {
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $employee1->id,
                'from_status' => ExpenseStatus::DRAFT->value,
                'to_status' => ExpenseStatus::SUBMITTED->value,
            ]);
        }

        // 2 APPROVED pour employee2
        $approvedExpenses = Expense::factory()->count(2)->create([
            'user_id' => $employee2->id,
            'status' => ExpenseStatus::APPROVED,
        ]);
        // Créer des logs pour ces dépenses
        foreach ($approvedExpenses as $expense) {
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $employee2->id,
                'from_status' => ExpenseStatus::DRAFT->value,
                'to_status' => ExpenseStatus::SUBMITTED->value,
            ]);
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $manager->id,
                'from_status' => ExpenseStatus::SUBMITTED->value,
                'to_status' => ExpenseStatus::APPROVED->value,
            ]);
        }

        // 2 REJECTED pour employee2 (avec commentaires)
        $rejectedExpenses = Expense::factory()->count(2)->create([
            'user_id' => $employee2->id,
            'status' => ExpenseStatus::REJECTED,
        ]);
        // Créer des logs et commentaires pour ces dépenses
        foreach ($rejectedExpenses as $expense) {
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $employee2->id,
                'from_status' => ExpenseStatus::DRAFT->value,
                'to_status' => ExpenseStatus::SUBMITTED->value,
            ]);
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $manager->id,
                'from_status' => ExpenseStatus::SUBMITTED->value,
                'to_status' => ExpenseStatus::REJECTED->value,
            ]);
            // Ajouter un commentaire du manager expliquant le rejet
            Comment::create([
                'expense_id' => $expense->id,
                'user_id' => $manager->id,
                'content' => 'Dépense non justifiée ou montant trop élevé. Veuillez fournir plus de détails.',
            ]);
        }

        // 2 PAID pour employee1
        $paidExpenses = Expense::factory()->count(2)->create([
            'user_id' => $employee1->id,
            'status' => ExpenseStatus::PAID,
        ]);
        // Créer des logs pour ces dépenses
        foreach ($paidExpenses as $expense) {
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $employee1->id,
                'from_status' => ExpenseStatus::DRAFT->value,
                'to_status' => ExpenseStatus::SUBMITTED->value,
            ]);
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $manager->id,
                'from_status' => ExpenseStatus::SUBMITTED->value,
                'to_status' => ExpenseStatus::APPROVED->value,
            ]);
            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' => $manager->id,
                'from_status' => ExpenseStatus::APPROVED->value,
                'to_status' => ExpenseStatus::PAID->value,
            ]);
        }
    }
}
