<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseLog extends Model
{
    use HasFactory;

    /**
     * Disable updated_at timestamp
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'expense_id',
        'user_id',
        'from_status',
        'to_status',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => ExpenseStatus::class,
            'to_status' => ExpenseStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
