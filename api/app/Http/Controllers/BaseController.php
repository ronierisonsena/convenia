<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Throwable;

class BaseController extends Controller
{
    public function saveExceptionLog(Throwable $e): void
    {
        Log::error($e->getMessage(), [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'trace' => collect($e->getTrace())->take(5),
        ]);
    }

    /**
     * Validate policy
     */
    public function validatePolicy(string $policyMethod, ?User $collaborator, Request $request): void
    {
        throw_if(
            $request->user()->cannot($policyMethod, $collaborator ?? User::class),
            new Exception('Forbidden', Response::HTTP_FORBIDDEN)
        );
    }
}
