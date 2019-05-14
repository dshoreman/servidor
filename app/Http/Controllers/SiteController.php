<?php

namespace Servidor\Http\Controllers;

use Servidor\Site;
use Servidor\Rules\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:sites,name',
            'primary_domain' => [new Domain],
            'is_enabled' => 'boolean',
        ]);

        $site = Site::create($data);

        return response($site, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \Servidor\Site $site
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Servidor\Site $site
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Servidor\Site           $site
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('sites', 'name')->ignore($site->id),
            ],
            'primary_domain' => [new Domain],
            'type' => 'required|in:basic,php,laravel,redirect',
            'source_repo' => 'required_unless:type,redirect|nullable|url',
            'document_root' => 'required_unless:type,redirect|nullable|string',
            'redirect_type' => 'required_if:type,redirect|nullable|integer',
            'redirect_to' => 'required_if:type,redirect|nullable|string',
            'is_enabled' => 'boolean',
        ]);

        $site->update($data);

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
