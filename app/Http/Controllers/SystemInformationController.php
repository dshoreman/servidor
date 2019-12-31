<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Request;
use Servidor\StatsBar;

class SystemInformationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return StatsBar::stats();
    }
}
