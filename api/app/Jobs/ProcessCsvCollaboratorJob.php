<?php

namespace App\Jobs;

use App\Mail\JobNotificationMail;
use App\Models\User;
use App\Services\CsvCollaboratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessCsvCollaboratorJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $filePath,
        private User $collaborator
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CsvCollaboratorService $collaboratorService): void
    {
        try {
            $collaborator = $this->collaborator;
            $fullPath = Storage::path($this->filePath);

            $collaboratorsNotImported = $collaboratorService->importCsv($fullPath, $collaborator->manager->id);

            Storage::delete($this->filePath);

            // Send Mail
            Mail::to($collaborator->email)->send(new JobNotificationMail($collaborator->name, $collaboratorsNotImported));

        } catch (Throwable $e) {
            Log::error($e->getMessage(), [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => collect($e->getTrace())->take(5),
            ]);
        }
    }
}
