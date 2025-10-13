<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyCollaboratorRequest;
use App\Models\User;
use App\Services\CollaboratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class DestroyCollaboratorController extends BaseController
{
    /**
     * DestroyCollaboratorController constructor.
     */
    public function __construct(
        private CollaboratorService $collaboratorService,
    ) {}

    /**
     * @OA\Delete(
     *     path="/api/v1/collaborator/{id}",
     *     tags={"Collaborator"},
     *     summary="Delete a collaborator",
     *     security={{"bearer": {}}},
     *
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="User ID"
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Collaborator deleted.",
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
    public function __invoke(DestroyCollaboratorRequest $request, User $collaborator): JsonResponse
    {
        try {
            $this->collaboratorService->destroy($collaborator);

            return response()
                ->json(['message' => __('responses.collaborator.deleted')])
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (Throwable $e) {
            $this->saveExceptionLog($e);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
