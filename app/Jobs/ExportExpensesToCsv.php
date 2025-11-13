<?php

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Models\Expense;
use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExportExpensesToCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance
     *
     * @param Export $export Export model instance
     * @param int $userId User ID requesting the export
     * @param bool $isManager Whether user is a manager
     * @param array $filters Export filters (from_date, to_date, status, category)
     */
    public function __construct(
        public Export $export,
        public int $userId,
        public bool $isManager,
        public array $filters = []
    ) {
    }

    /**
     * Execute the job to generate CSV export
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $this->export->update(['status' => ExportStatus::PROCESSING]);

            // Build query based on user role
            $query = $this->isManager
                ? Expense::query()
                : Expense::where('user_id', $this->userId);

            $query->with('user')
                ->when($this->filters['from_date'] ?? null, fn($q, $date) => $q->whereDate('spent_at', '>=', $date))
                ->when($this->filters['to_date'] ?? null, fn($q, $date) => $q->whereDate('spent_at', '<=', $date))
                ->when($this->filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
                ->when($this->filters['category'] ?? null, fn($q, $category) => $q->where('category', $category))
                ->orderBy('spent_at', 'desc');

            $expenses = $query->get();

            $csv = $this->generateCsv($expenses);

            // Store CSV file
            $fileName = "expenses_{$this->export->id}_" . now()->timestamp . '.csv';
            $filePath = "exports/{$fileName}";
            Storage::put($filePath, $csv);

            // Update export with success status
            $this->export->update([
                'status' => ExportStatus::READY,
                'file_path' => $filePath,
            ]);
        } catch (Throwable $e) {
            // Update export with failure status
            $this->export->update([
                'status' => ExportStatus::FAILED,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate CSV content from expenses collection
     *
     * @param \Illuminate\Support\Collection $expenses Collection of expenses
     * @return string CSV content
     */
    private function generateCsv($expenses): string
    {
        $csv = [];

        // Add CSV headers
        $csv[] = [
            'ID',
            'User Name',
            'User Email',
            'Title',
            'Amount',
            'Currency',
            'Spent At',
            'Category',
            'Status',
            'Receipt Path',
            'Created At',
            'Updated At',
        ];

        // Add expense data rows
        foreach ($expenses as $expense) {
            $csv[] = [
                $expense->id,
                $expense->user->name,
                $expense->user->email,
                $expense->title,
                $expense->amount,
                $expense->currency,
                $expense->spent_at->format('Y-m-d'),
                $expense->category->value,
                $expense->status->value,
                $expense->receipt_path ?? '',
                $expense->created_at->format('Y-m-d H:i:s'),
                $expense->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        // Convert array to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }
}
