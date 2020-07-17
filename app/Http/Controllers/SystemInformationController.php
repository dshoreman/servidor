<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Servidor\StatsBar;

class SystemInformationController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(StatsBar::stats());
    }
}
