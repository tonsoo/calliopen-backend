<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/documentation/yaml', function () {
    $dir = config('l5-swagger.defaults.paths.docs');
    $file = config('l5-swagger.documentations.default.paths.docs_yaml');
    $path = $dir.'/'.$file;
    if (!File::exists($path)) {
        abort(404);
    }

    return Response::file($path, [
        'Content-Type' => 'application/yaml',
        'Content-Disposition' => 'inline;filename="client-openapi.yaml"',
    ]);
});
