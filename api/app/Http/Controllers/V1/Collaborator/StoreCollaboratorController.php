<?php

namespace App\Http\Controllers\V1\Collaborator;

use App\Http\Controllers\Controller;
use App\Http\Requests\CollaboratorRequest;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\StaffResource;
use App\Http\Resources\UserResource;
use App\Services\CollaboratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreCollaboratorController extends Controller
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
     *     security={{"bearer": {}}},
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/CollaboratorRequest")
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
    public function __invoke(CollaboratorRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $data = $request->validated();
            $data['manager_id'] = $user?->manager?->id;

            $user = $this->collaboratorService->store($data);

            $model = match ($user->type->role) {
                'manager' => (new ManagerResource($user->manager))->setToken($user->newToken),
                'staff' => (new StaffResource($user->staff))->setToken($user->newToken),
            };

            return $model->additional(['message' => __('responses.collaborator.created')])
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
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
