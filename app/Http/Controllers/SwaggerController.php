<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class SwaggerController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('swagger.index', [
            'urlToDocs' => route('documentation.api'),
        ]);
    }

    public function api(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $content = '';
        try
        {
            $content = File::get(storage_path('swagger/swagger.yaml'));
        } catch (FileNotFoundException $e)
        {
            abort(404, "File not found");
        }
        return response($content, 200, [
            'Content-Type' => 'application/yaml',
            'Content-Disposition' => 'inline',
        ]);
    }
}
