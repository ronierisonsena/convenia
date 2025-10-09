<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    /**
     * Register new collaborator
     *
     * @return UserResource|JsonResponse
     */
    public function register(AuthRequest $request): UserResource|JsonResponse
    {
        try {
            $user = $this->userService->register($request->validated());

            return (new UserResource($user))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Error in '. get_class(), [
                'exception' => $e,
                'code' => 'register_error',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User Login
     *
     * @return UserResource|JsonResponse
     */
    public function login(Request $request): UserResource|JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $user = $this->userService->login($credentials);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error in '. get_class(), [
                'exception' => $e,
                'code' => 'login_error',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User logout
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->token()->revoke();

            return response()->json([
                'message' => 'Logged off!',
            ]);
        } catch (Exception $e) {
            Log::error('Error in '. get_class(), [
                'exception' => $e,
                'code' => 'logout_error',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return logged user
     *
     * @return JsonResponse
     */
    public function me(Request $request)
    {
        try {
            return (new UserResource($request->user()))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error in '. get_class(), [
                'exception' => $e,
                'code' => 'me_error',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
