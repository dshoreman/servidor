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
     * Output a list of files from the local filesystem.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $path = $request->get('path');
        $results = $this->fm->list($path);

        return (array) $results;
    }
}
