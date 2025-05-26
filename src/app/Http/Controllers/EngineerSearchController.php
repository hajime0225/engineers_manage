<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Engineer;
use App\Models\Skill;

class EngineerSearchController extends Controller
{
    /**
     * エンジニア検索フォームを表示します。
     */
    public function showSearchForm()
    {
        $skills = Skill::orderBy('type')->orderBy('name')->get(); // スキル種別と名前でソート
        return view('engineers.search_form', compact('skills'));
    }

}
