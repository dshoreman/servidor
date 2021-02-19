<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\FileManager\PathNotFound;
use Servidor\FileManager\UnsupportedFileType;

class ListOrShowPath extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = [];
        $status = Response::HTTP_OK;
        $file = (string) $request->get('file');
        $path = $file ?: (string) $request->get('path');

        try {
            $data = $file ? $this->fm->open($path) : $this->fm->list($path);
        } catch (PathNotFound $e) {
            $error = $e->getMessage();
            $status = Response::HTTP_NOT_FOUND;
        } catch (UnsupportedFileType $e) {
            $error = $e->getMessage();
            $status = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
        }

        return response()->json(isset($error) ? [
            'filename' => $path,
            'filepath' => $path,
            'error' => ['code' => $status, 'msg' => $error],
        ] : $data, $status);
    }
}
