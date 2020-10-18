<?php

use Illuminate\Support\Facades\Route;

if (config('doc.laravel_version') >= 8)
{
    Route::get('doc', [\Zning\Apidocument\controller\ApiDocumentController::class, 'index'])->name('docapi.index');
    Route::get('doc/show', [\Zning\Apidocument\controller\ApiDocumentController::class, 'show'])->name('docapi.show');
    Route::post('doc/api', [\Zning\Apidocument\controller\ApiDocumentController::class, 'api'])->name('docapi.api');
}else
{
    Route::post('doc/api','Zning\Apidocument\controller\ApiDocumentController@api')->name('docapi.api');
    Route::get('doc/show','Zning\Apidocument\controller\ApiDocumentController@show')->name('docapi.show');
    Route::get('doc','Zning\Apidocument\controller\ApiDocumentController@index')->name('docapi.index');
}

