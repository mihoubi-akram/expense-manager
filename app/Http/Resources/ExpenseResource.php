<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'spent_at' => $this->spent_at?->format('Y-m-d'),
            'category' => $this->category,
            'receipt_path' => $this->receipt_path,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'comments' => $this->whenLoaded('comments'),
            'logs' => $this->whenLoaded('logs'),
        ];
    }
}
