<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FallbackController extends Controller
{
    public function api(): void
    {
        throw new NotFoundHttpException();
    }

    public function frontend(): Response
    {
        return response()->view('servidor');
    }
}
