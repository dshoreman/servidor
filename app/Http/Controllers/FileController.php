<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Request;
use InvalidArgumentException;
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
            $file = $this->fm->open($filepath);

            if (array_key_exists('error', $file)) {
                return response($file, $file['error']['code']);
            }

            try {
                return response()->json($file);
            } catch (InvalidArgumentException $e) {
                return ['error' => ['code' => 422, 'msg' => 'Failed loading file']];
            }
        }

        $path = $request->get('path');

        return response()->json($this->fm->list($path));
    }
}
