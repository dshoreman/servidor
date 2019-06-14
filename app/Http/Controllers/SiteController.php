<?php

namespace Servidor\Http\Controllers;

use Servidor\Http\Requests\CreateSite;
use Servidor\Http\Requests\UpdateSite;
use Servidor\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Site::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateSite $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSite $request)
    {
        $site = Site::create($request->validated());

        return response($site, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSite     $request
     * @param \Servidor\Site $site
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
     * @param \Servidor\Site $site
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
