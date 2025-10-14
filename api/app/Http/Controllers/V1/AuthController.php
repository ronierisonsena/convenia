<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @OA\Info (
 *     title="Collaborators API - By Convenia ❤",
 *     version="1.0.0",
 *     description="API for managing collaborators"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="OAuth2",
 *     description="Bearer token obtained from the login endpoint."
 * ),
 *
 * @OA\SecurityScheme(
 *     securityScheme="api_key",
 *     type="apiKey",
 *     in="header",
 *     name="api-key",
 *     description="API key for requests."
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints for user login, logout and me."
 * ),
 *
 * @OA\Tag(
 *     name="Collaborator",
 *     description="Endpoints for create, update, get all, import and delete collaborators."
 * )
 */
class AuthController extends BaseController
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
     *     @OA\Parameter(
     *         name="api-key",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="9cff43c8a441e76e2abf83c56ab0348f"
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", example="manager@example.com"),
     *             @OA\Property(property="password", type="string", example="123456")
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
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $user = $this->userService->login($credentials);

            return (new UserResource($user))
                ->setToken($user->newAccessToken)
                ->additional([
                    'message' => __('responses.login_successful'),
                ])
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Throwable $e) {
            $this->saveExceptionLog($e);

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
     *     security={{"bearerAuth":{}, {"api_key": {}}}},
     *
     *     @OA\Parameter(
     *         name="api-key",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="9cff43c8a441e76e2abf83c56ab0348f"
     *     ),
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
            $this->saveExceptionLog($e);

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
     *     security={{"bearerAuth":{}, {"api_key": {}}}},
     *
     *     @OA\Parameter(
     *         name="api-key",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="9cff43c8a441e76e2abf83c56ab0348f"
     *     ),
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
            $this->saveExceptionLog($e);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
