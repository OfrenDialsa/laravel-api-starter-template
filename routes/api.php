<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

function loadModuleRoutes($module)
{
    $path = base_path("app/Modules/{$module}/routes.php");
    if (file_exists($path)) require $path;
}

loadModuleRoutes('Auth');
