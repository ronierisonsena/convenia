<?php

namespace App\Http\Controllers\V1\Collaborator;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreCollaboratorRequest;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\StaffResource;
use App\Services\CollaboratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class StoreCollaboratorController extends BaseController
{
    /**
     * StoreCollaboratorController constructor.
     */
    public function __construct(
        private CollaboratorService $collaboratorService,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/collaborator",
     *     tags={"Collaborator"},
     *     summary="Create a new collaborator",
     *     security={{"bearerAuth":{}, {"api_key": {}}}},
     *
     *     @OA\Parameter(
     *         name="api-key",
     *         in="header",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="9cff43c8a441e76e2abf83c56ab0348f"
     *     ),
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/StoreCollaboratorRequest")
     *     ),
     *
     *     @OA\Response(
     *          response=201,
     *          description="Created",
     *
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable entity",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(
     *                  property="message",
     *                  type="object",
     *                  @OA\Property(
     *                      property="cpf",
     *                      type="object",
     *                      example="Invalid CPF. Acceptable formats are: 000.000.000-00 | 00000000000"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="object",
     *                      example="The email has already been taken."
     *                  ),
     *              ),
     *          ),
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Resource not found",
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Error processing request"
     *              ),
     *          ),
     *     )
     * )
     */
    public function __invoke(StoreCollaboratorRequest $request): JsonResponse
    {
        try {
            $this->validatePolicy(policyMethod: 'create', request: $request, collaborator: null);

            $data = $request->validated();
            $user = auth()->user();
            $data['manager_id'] = $user?->manager?->id;

            $user = $this->collaboratorService->store($data);

            $resource = match ($user->type->role) {
                'manager' => (new ManagerResource($user->manager))->setToken($user->newAccessToken),
                'staff' => (new StaffResource($user->staff))->setToken($user->newAccessToken),
            };

            return $resource->additional(['message' => __('responses.collaborator.created')])
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (Throwable $e) {
            $this->saveExceptionLog($e);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
