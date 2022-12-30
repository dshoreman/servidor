<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\FileManager\PathNotFound;
use Servidor\Http\Requests\FileManager\EditFile as EditFileRequest;

class EditFile extends Controller
{
    public function __invoke(EditFileRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $this->fm->open($data['file']);
        } catch (PathNotFound $e) {
            return $this->jsonError($e);
        }

        if ($file['contents'] === $data['contents']) {
            return response()->json('', Response::HTTP_NOT_MODIFIED);
        }

        $this->fm->save($data['file'], $data['contents']);

        return response()->json($this->fm->open($data['file']));
    }

    private function jsonError(PathNotFound $e): JsonResponse
    {
        $status = Response::HTTP_NOT_FOUND;

        return response()->json([
            'error' => [
                'code' => $status,
                'msg' => $e->getMessage(),
            ],
        ], $status);
    }
}
