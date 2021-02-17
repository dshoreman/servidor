<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\FileManager\FileManager;
use Servidor\FileManager\PathDeletionFailed;
use Servidor\FileManager\PathNotFound;
use Servidor\FileManager\PathNotWritable;
use Servidor\FileManager\UnsupportedFileType;
use Symfony\Component\HttpFoundation\Response as SfResponse;

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
     */
    public function index(Request $request): JsonResponse
    {
        $data = [];
        $status = Response::HTTP_OK;
        $file = $request->get('file');
        $path = $file ?? $request->get('path');

        try {
            $data = $file ? $this->fm->open($path) : $this->fm->list($path);
        } catch (PathNotFound $e) {
            $error = $e->getMessage();
            $status = Response::HTTP_NOT_FOUND;
        } catch (UnsupportedFileType $e) {
            $error = $e->getMessage();
            $status = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
        } finally {
            return response()->json(isset($error) ? [
                'filename' => $path,
                'filepath' => $path,
                'error' => ['code' => $status, 'msg' => $error],
            ] : $data, $status);
        }
    }

    public function create(Request $request): JsonResponse
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

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     */
    public function update(Request $request)
    {
        if (!($filepath = $request->query('file')) || !is_string($filepath)) {
            throw ValidationException::withMessages(['file' => 'File path must be specified.']);
        }

        $data = $request->validate([
            'file' => 'required',
            'contents' => 'required|nullable',
        ]);

        try {
            $file = $this->fm->open($filepath);
        } catch (PathNotFound $e) {
            $status = Response::HTTP_NOT_FOUND;

            return response()->json([
                'error' => ['code' => $status, 'msg' => $e->getMessage()],
            ], $status);
        }

        if ($file['contents'] == $data['contents']) {
            return response(null, Response::HTTP_NOT_MODIFIED);
        }

        $this->fm->save($filepath, $data['contents']);

        return response()->json($this->fm->open($filepath));
    }

    public function rename(Request $request): JsonResponse
    {
        $data = $request->validate([
            'oldPath' => 'required|string',
            'newPath' => 'required|string',
        ]);

        $file = $this->fm->move($data['oldPath'], $data['newPath']);

        return response()->json($file, $file['error']['code'] ?? Response::HTTP_OK);
    }

    /**
     * @return Response|JsonResponse
     */
    public function delete(Request $request): SfResponse
    {
        if (!($filepath = $request->query('file')) || !is_string($filepath)) {
            throw ValidationException::withMessages(['file' => 'File path must be specified.']);
        }

        try {
            $this->fm->delete($filepath);
        } catch (PathNotWritable $e) {
            return response()->json($e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (PathDeletionFailed $e) {
            return response()->json($e->getMessage(), Response::HTTP_NOT_MODIFIED);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
