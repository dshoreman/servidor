<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateNode extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dir' => 'required_without:file|string',
            'file' => 'required_without:dir|string',
            'contents' => 'required_with:file|nullable',
        ]);

        $res = $data['dir'] ?? null ? $this->fm->createDir($data['dir'])
             : $this->fm->createFile($data['file'], $data['contents']);

        return response()->json($res, $res['error']['code'] ?? Response::HTTP_CREATED);
    }
}
