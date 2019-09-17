<?php

namespace Servidor\Http\Controllers;

use Servidor\StatsBar;
use Illuminate\Http\Request;

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
