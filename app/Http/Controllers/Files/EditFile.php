<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\FileManager\PathNotFound;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class EditFile extends Controller
{
    /** @return JsonResponse|Response */
    public function __invoke(Request $request): BaseResponse
    {
        if (!($filepath = $request->query('file')) || !is_string($filepath)) {
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

        if ($file['contents'] == $data['contents']) {
            return response(null, Response::HTTP_NOT_MODIFIED);
        }

        $this->fm->save($filepath, $data['contents']);

        return response()->json($this->fm->open($filepath));
    }
}
