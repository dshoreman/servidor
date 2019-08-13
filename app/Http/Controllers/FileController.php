<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Request;
use Servidor\FileManager\FileManager;

class FileController extends Controller
{
    /**
     * @var FileManager
     */
    private $fm;

    public function __construct(FileManager $manager)
    {
        $this->fm = $manager;
    }

    /**
     * Output a file or list of files from the local filesystem.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($filepath = $request->get('file')) {
            return $this->fm->open($filepath);
        }

        $path = $request->get('path');

        return $this->fm->list($path);
    }
}
