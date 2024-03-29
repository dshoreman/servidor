<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\FileManager\PathNotWritable;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class DeletePath extends Controller
{
    /** @return \Illuminate\Http\JsonResponse|Response */
    public function __invoke(Request $request): BaseResponse
    {
        $filepath = $request->query('file');

        if (!$filepath || !\is_string($filepath)) {
            throw ValidationException::withMessages(['file' => 'File path must be specified.']);
        }

        try {
            $this->fm->delete($filepath);
        } catch (PathNotWritable $e) {
            return response()->json($e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        return response()->noContent();
    }
}
