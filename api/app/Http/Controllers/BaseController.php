<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Throwable;

class BaseController extends Controller
{
    /**
     * @param Throwable $e
     */
    public function saveExceptionLog(Throwable $e): void
    {
        Log::error($e->getMessage(), [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'trace' => collect($e->getTrace())->take(5),
        ]);
    }
}
