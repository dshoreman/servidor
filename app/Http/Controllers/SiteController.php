<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Http\Requests\CreateSite;
use Servidor\Http\Requests\UpdateSite;
use Servidor\Site;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Site::all()->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSite $request)
    {
        $site = Site::create($request->validated());

        return response($site, Response::HTTP_CREATED);
    }

    /**
     * Display a list of branches on the given site's repository.
     *
     * @return Response
     */
    public function branches(Request $request, Site $site)
    {
        $cmd = "git ls-remote --heads '%s' | sed 's^.*refs/heads/^^'";
        $repo = $request->query('repo', $site->source_repo);

        exec(sprintf($cmd, $repo), $branches);

        return $branches;
    }

    /**
     * Pull the latest commit from Git.
     *
     * @return \Illuminate\Http\Response
     */
    public function pull(Site $site)
    {
        $root = $site->document_root;
        $branch = $site->source_branch;

        if (!$site->type || 'redirect' == $site->type) {
            $error = 'Project type does not support pull.';
        } elseif (!$root) {
            $error = 'Project is missing its document root!';
        }

        if (isset($error)) {
            return response(compact('error'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_dir($root . '/.git')) {
            $args = $branch ? ' && git checkout "' . $branch . '"' : '';

            exec('cd "' . $root . '"' . $args . ' && git pull');

            return response($site, Response::HTTP_OK);
        }

        if (!is_dir(dirname($root))) {
            mkdir(dirname($root));
        }

        $args = $branch ? ' --branch "' . $branch . '"' : '';
        $paths = ' "' . $site->source_repo . '" "' . $root . '"';

        $cmd = 'git clone' . $args . $paths;

        exec($cmd);

        return response($site, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSite $request, Site $site)
    {
        $site->update($request->validated());

        return response($site, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
