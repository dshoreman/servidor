<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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

            return response()->json($file);
        }

        $path = $request->get('path');
        $list = $this->fm->list($path);

        return isset($list['error'])
            ? response($list, $list['error']['code'])
            : response()->json($list);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'dir' => 'required_without:file|string',
            'file' => 'required_without:dir|string',
            'contents' => 'required_with:file|nullable',
        ]);

        $res = $data['dir'] ?? null ? $this->fm->createDir($data['dir'])
             : $this->fm->createFile($data['file'], $data['contents']);

        return response()->json($res, $res['error']['code'] ?? Response::HTTP_CREATED);
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

    public function rename(Request $request)
    {
        $data = $request->validate([
            'oldPath' => 'required|string',
            'newPath' => 'required|string',
        ]);

        $file = $this->fm->move($data['oldPath'], $data['newPath']);

        return response()->json($file, $file['error']['code'] ?? Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        if (!$filepath = $request->query('file')) {
            throw ValidationException::withMessages(['file' => 'File path must be specified.']);
        }

        $data = $this->fm->delete($filepath);

        return null === $data['error']
            ? response(null, Response::HTTP_NO_CONTENT)
            : response()->json($data, $data['error']['code']);
    }
}
