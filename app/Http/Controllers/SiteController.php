<?php

namespace Servidor\Http\Controllers;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:sites,name',
            'primary_domain' => 'url',
            'is_enabled' => 'boolean',
        ]);

        $site = Site::create($data);

        return response($site, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Servidor\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Servidor\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Servidor\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:sites,name',
            'primary_domain' => 'url',
            'is_enabled' => 'boolean',
        ]);

        $site->update($data);

        return response($site, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Servidor\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
