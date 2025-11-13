<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'period' => $this->resource['period'],
            'total_amount' => $this->resource['total_amount'],
            'currency' => 'EUR',
            'expenses_count' => $this->resource['expenses_count'],
            'by_status' => $this->resource['by_status'],
            'by_category' => $this->resource['by_category'],
        ];
    }
}
