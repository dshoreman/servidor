<?php

namespace Servidor\Http\Controllers\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MovePath extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'oldPath' => 'required|string',
            'newPath' => 'required|string',
        ]);

        $file = $this->fm->move((string) $data['oldPath'], (string) $data['newPath']);

        return response()->json($file, (int) (
            $file['error']['code'] ?? Response::HTTP_OK
        ));
    }
}
