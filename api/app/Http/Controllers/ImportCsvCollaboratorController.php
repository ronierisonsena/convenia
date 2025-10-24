<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCsvCollaboratorRequest;
use App\Jobs\ProcessCsvCollaboratorJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class ImportCsvCollaboratorController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/collaborator/import/csv",
     *     tags={"Collaborator"},
     *     summary="Import collaborators from CSV.",
     *     description="All collaborators created has the six first numbers of their cpf like password ",
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
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="CSV file to import."
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Processing the file CSV received.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Processing CSV file, you will receive an email when job's done."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 example={
     *                     "file": {"File has to be CSV."}
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Error processing request.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error processing request."
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke(ImportCsvCollaboratorRequest $request): JsonResponse
    {
        try {
            $this->validatePolicy(policyMethod: 'importCsv', collaborator: null, request: $request);

            $path = $request->file('file')->store('collaborator/csv');

            ProcessCsvCollaboratorJob::dispatch($path, auth()->user());

            return response()
                ->json([
                    'message' => __('responses.collaborator.importing_csv'),
                ])
                ->setStatusCode(Response::HTTP_OK);
        } catch (Throwable $e) {
            $this->saveExceptionLog($e);

            return response()->json([
                'message' => __('responses.error_on_request'),
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
