<?php

namespace App\Http\Controllers;

use App\Site;
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Site  $site
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
}
