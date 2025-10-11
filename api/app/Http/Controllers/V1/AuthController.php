<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @OA\Info (
 *     title="Collaborators API",
 *     version="1.0.0",
 *     description="API for managing collaborators"
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User login",
     *     description="Authenticate user and return user data with token.",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthenticated or privileges missing."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $user = $this->userService->login($credentials);

            return (new UserResource($user))
                ->setToken($user->newToken)
                ->additional([
                    'message' => __('responses.login_successful'),
                ])
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => collect($e->getTrace())->take(5),
            ]);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="User logout",
     *     description="Revoke user token and logout.",
     *     operationId="logout",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Logged off!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->token()->revoke();

            return response()->json([
                'message' => __('responses.logout'),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => collect($e->getTrace())->take(5),
            ]);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     summary="Get logged user",
     *     description="Return the authenticated user info.",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function me(Request $request)
    {
        try {
            return (new ManagerResource($request->user()->manager))
                ->additional(['message' => 'User found.'])
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error($e->getMessage(), [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => collect($e->getTrace())->take(5),
            ]);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
