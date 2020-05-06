<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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
     * @return \Illuminate\Http\JsonResponse|Response
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
                $file['contents'] = '';
                $file['error'] = [
                    'code' => 422,
                    'msg' => 'Failed loading file',
                ];

                return response()->json($file);
            }
        }

        $path = $request->get('path');

        return response()->json($this->fm->list($path));
    }

    public function update(Request $request)
    {
        if (!$filepath = $request->query('file')) {
            throw ValidationException::withMessages(['file' => 'File path must be specified.']);
        }

        $data = $request->validate([
            'file' => 'required',
            'contents' => 'required|nullable',
        ]);

        $file = $this->fm->open($filepath);

        if (array_key_exists('error', $file)) {
            return response($file, $file['error']['code']);
        }

        if ($file['contents'] == $data['contents']) {
            return response(null, Response::HTTP_NOT_MODIFIED);
        }

        $this->fm->save($filepath, $data['contents']);

        return response()->json($this->fm->open($filepath));
    }
}
