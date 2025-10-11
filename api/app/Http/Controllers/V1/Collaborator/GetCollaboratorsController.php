<?php

namespace App\Http\Controllers\V1\Collaborator;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetCollaboratorRequest;
use App\Http\Resources\StaffResource;
use App\Http\Resources\UserResource;
use App\Services\CollaboratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class GetCollaboratorsController extends BaseController
{
    /**
     * StoreCollaboratorController constructor.
     */
    public function __construct(
        private CollaboratorService $collaboratorService,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/collaborators",
     *     tags={"Collaborator"},
     *     summary="Get all collaborators",
     *     security={{"bearer": {}}},
     *
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="",
     *          @OA\Schema(type="string", example="Ted Rubber")
     *     ),
     *
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="",
     *          @OA\Schema(type="string", example="test@email.com")
     *     ),
     *
     *     @OA\Parameter(
     *          name="cpf",
     *          in="query",
     *          description="",
     *          @OA\Schema(type="string", example="111.222.333-45")
     *     ),
     *
     *     @OA\Parameter(
     *          name="city",
     *          in="query",
     *          description="",
     *          @OA\Schema(type="string", example="Belo Horizonte")
     *     ),
     *
     *     @OA\Parameter(
     *          name="state",
     *          in="query",
     *          description="",
     *          @OA\Schema(type="string", example="Minas Gerais")
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Accepted",
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
     *                      property="email",
     *                      type="object",
     *                      example="Max 150 caracteres."
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
    public function __invoke(GetCollaboratorRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $user = auth()->user();

            $collaborators = $this->collaboratorService->index($user, $filters);

            return StaffResource::collection($collaborators)
                ->additional(['message' => __('responses.collaborator.index')])
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Throwable $e) {
            $this->saveExceptionLog($e);

            return response()->json([
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => collect($e->getTrace())->take(5),
                // 'message' => __('responses.error_on_request'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
