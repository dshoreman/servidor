<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\FileManager\PathNotFound;

class EditFile extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $filepath = $request->query('file');

        if (!$filepath || !is_string($filepath)) {
            throw ValidationException::withMessages(['file' => 'File path must be specified.']);
        }

        $data = $request->validate([
            'file' => 'required',
            'contents' => 'required|nullable',
        ]);

        try {
            $file = $this->fm->open($filepath);
        } catch (PathNotFound $e) {
            $status = Response::HTTP_NOT_FOUND;

            return response()->json([
                'error' => ['code' => $status, 'msg' => $e->getMessage()],
            ], $status);
        }

        if ($file['contents'] === $data['contents']) {
            return response()->json('', Response::HTTP_NOT_MODIFIED);
        }

        $this->fm->save($filepath, (string) $data['contents']);

        return response()->json($this->fm->open($filepath));
    }
}
