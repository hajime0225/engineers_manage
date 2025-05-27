<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EngineerSearchController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return 'ハローワールド from Laravel!';
});

Route::get('/hello', function () {
    return 'ハローワールド from Laravel on /hello!';
});

// エンジニア検索フォーム表示
Route::get('/engineers/search', [EngineerSearchController::class, 'showSearchForm'])->name('engineers.searchForm');

// エンジニア検索実行と結果表示
Route::get('/engineers/show', [EngineerSearchController::class, 'searchEngineers'])->name('engineers.show');
