<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Response;
use Servidor\StatsBar;

class SystemInformationController extends Controller
{
    public function __invoke(): Response
    {
        return response()->json(StatsBar::stats());
    }
}
