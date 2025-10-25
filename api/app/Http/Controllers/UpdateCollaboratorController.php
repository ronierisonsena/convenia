<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCollaboratorRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\CollaboratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Throwable;

class UpdateCollaboratorController extends BaseController
{
    /**
     * UpdateCollaboratorController constructor.
     */
    public function __construct(
        private CollaboratorService $collaboratorService,
    ) {}

    /**
     * @OA\Put(
     *     path="/api/v1/collaborator/{id}",
     *     tags={"Collaborator"},
     *     summary="Update a collaborator",
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
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="User ID"
     *     ),
     *
     *     @OA\RequestBody(
     *
     *          @OA\JsonContent(ref="#/components/schemas/UpdateCollaboratorRequest")
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Collaborator updated.",
     *
     *          @OA\JsonContent(ref="#/components/schemas/UserResource"),
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
    public function __invoke(UpdateCollaboratorRequest $request, User $collaborator): JsonResponse
    {
        try {
            $this->validatePolicy('update', $collaborator, $request);

            $user = auth()->user();
            $data = Arr::except($request->validated(), ['collaborator']);
            $userCollaborator = $this->collaboratorService->update($data, $collaborator, $user);

            return (new UserResource($userCollaborator))->additional(['message' => __('responses.collaborator.updated')])
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
