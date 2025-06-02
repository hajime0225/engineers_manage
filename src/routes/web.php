<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminMasterDataController;
use App\Http\Controllers\EngineerSearchController;


Route::get('/', function () {
    return 'ハローワールド from Laravel!';
});

Route::get('/hello', function () {
    return 'ハローワールド from Laravel on /hello!';
});

// ---------------------------------------------------------------------
// エンジニア画面
// ---------------------------------------------------------------------
// エンジニア検索フォーム表示
Route::get('/engineers/search', [EngineerSearchController::class, 'showSearchForm'])->name('engineers.searchForm');

// エンジニア検索結果表示
Route::get('/engineers/show', [EngineerSearchController::class, 'searchEngineers'])->name('engineers.show');

// エンジニア詳細表示
Route::get('/engineers/detail/{engineer}', [EngineerSearchController::class, 'showDetail'])->name('engineers.detail');

// ---------------------------------------------------------------------
// 管理画面
// ---------------------------------------------------------------------

// マスターデータ登録画面表示
Route::get('/admin/create', [AdminMasterDataController::class, 'create'])->name('admin.create');
// マスターデータ保存処理
Route::post('/admin/store', [AdminMasterDataController::class, 'store'])->name('admin.store');
// マスターデータ参照・編集・削除画面
Route::get('/admin/edit', [AdminMasterDataController::class, 'edit'])->name('admin.edit');
// マスターデータ更新処理
Route::patch('/admin/edit', [AdminMasterDataController::class, 'update'])->name('admin.update');
// マスターデータ削除処理
Route::delete('/admin/destroy', [AdminMasterDataController::class, 'destroy'])->name('admin.destroy');
