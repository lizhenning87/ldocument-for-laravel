<?php

use Illuminate\Support\Facades\Route;

if (config('doc.laravel_version') >= 8)
{
    Route::get('doc', [\Zning\Apidocument\controller\ApiDocumentController::class, 'index']);
}else
{
    Route::get('doc','Zning\Apidocument\controller\ApiDocumentController@index');
}

