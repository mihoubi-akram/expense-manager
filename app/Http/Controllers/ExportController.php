<?php

namespace App\Http\Controllers;

use App\Enums\ExportStatus;
use App\Http\Requests\Export\CreateExportRequest;
use App\Http\Resources\ExportResource;
use App\Jobs\ExportExpensesToCsv;
use App\Models\Export;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Create a new expense export
     *
     * @param CreateExportRequest $request Validated request with optional filters
     * @return ExportResource Export resource with PENDING status
     */
    public function store(CreateExportRequest $request): ExportResource
    {
        $this->authorize('create', Export::class);

        $user = $request->user();

        $export = Export::create([
            'user_id' => $user->id,
            'status' => ExportStatus::PENDING,
        ]);

        // Dispatch job to generate CSV
        ExportExpensesToCsv::dispatch(
            $export,
            $user->id,
            $user->isManager(),
            $request->validated()
        );

        return ExportResource::make($export);
    }

    /**
     * Get export details and status
     *
     * @param Export $export Export instance
     * @return ExportResource Export resource with current status
     */
    public function show(Export $export): ExportResource
    {
        $this->authorize('view', $export);

        return ExportResource::make($export);
    }

    /**
     * Download the generated CSV file
     *
     * @param Export $export Export instance
     * @return StreamedResponse CSV file download response
     */
    public function download(Export $export): StreamedResponse
    {
        $this->authorize('download', $export);

        // Check if file exists
        if (!Storage::exists($export->file_path)) {
            abort(404, 'Export file not found');
        }

        return response()->streamDownload(function () use ($export) {
            echo Storage::get($export->file_path);
        }, basename($export->file_path), [
            'Content-Type' => 'text/csv',
        ]);
    }
}
