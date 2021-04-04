<?php

namespace Servidor\Http\Controllers\Files;

use Servidor\FileManager\FileManager;
use Servidor\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    protected FileManager $fm;

    public function __construct(FileManager $manager)
    {
        $this->fm = $manager;
    }
}
