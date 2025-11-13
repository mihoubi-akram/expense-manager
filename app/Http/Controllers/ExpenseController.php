<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseStatus;
use App\Http\Requests\Expense\RejectExpenseRequest;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Comment;
use App\Models\Expense;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Expense::class);

        $expenses = Expense::query()
            ->with(['user', 'comments'])
            ->when(
                $request->user()->isEmployee(),
                fn($q) => $q->where('user_id', $request->user()->id)
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->from_date, fn($q) => $q->whereDate('spent_at', '>=', $request->from_date))
            ->when($request->to_date, fn($q) => $q->whereDate('spent_at', '<=', $request->to_date))
            ->latest()
            ->paginate(15);

        return ExpenseResource::collection($expenses);
    }

    public function store(StoreExpenseRequest $request): ExpenseResource
    {
        $expense = Expense::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'EUR',
            'spent_at' => $request->spent_at,
            'category' => $request->category,
            'receipt_path' => $request->receipt_path,
            'status' => ExpenseStatus::DRAFT,
        ]);

        return ExpenseResource::make($expense->load('user'));
    }

    public function show(Expense $expense): ExpenseResource
    {
        $this->authorize('view', $expense);

        return ExpenseResource::make($expense->load(['user', 'comments.user', 'logs.user']));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): ExpenseResource
    {
        $expense->update($request->validated());

        return ExpenseResource::make($expense->load('user'));
    }

    public function destroy(Expense $expense): Response
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return response()->noContent();
    }

    public function submit(Request $request, Expense $expense): ExpenseResource
    {
        $this->authorize('submit', $expense);

        $expense->update(['status' => ExpenseStatus::SUBMITTED]);

        return ExpenseResource::make($expense->load('user'));
    }

    public function approve(Request $request, Expense $expense): ExpenseResource
    {
        $this->authorize('approve', $expense);

        $expense->update(['status' => ExpenseStatus::APPROVED]);

        return ExpenseResource::make($expense->load('user'));
    }

    public function reject(RejectExpenseRequest $request, Expense $expense): ExpenseResource
    {
        DB::transaction(function () use ($request, $expense) {
            $expense->update(['status' => ExpenseStatus::REJECTED]);

            Comment::create([
                'expense_id' => $expense->id,
                'user_id' => $request->user()->id,
                'content' => $request->comment,
            ]);
        });

        return ExpenseResource::make($expense->load(['user', 'comments.user']));
    }

    public function pay(Request $request, Expense $expense): ExpenseResource
    {
        $this->authorize('pay', $expense);

        $expense->update(['status' => ExpenseStatus::PAID]);

        return ExpenseResource::make($expense->load('user'));
    }
}
