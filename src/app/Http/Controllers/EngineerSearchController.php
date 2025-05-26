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

    /**
     * エンジニアを検索し、結果を表示します。
     */
    public function searchEngineers(Request $request)
    {
        $query = Engineer::query()->with(['skills', 'projectExperiences']);

        // --- 検索条件の受付 ---
        $searchKeyword = $request->input('keyword');
        $selectedSkillIds = $request->input('skills', []);
        $experienceYearsMin = $request->input('experience_years_min');

        // キーワード検索 (氏名、自己PR、プロジェクト概要など)
        if (!empty($searchKeyword)) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('name', 'LIKE', "%{$searchKeyword}%")
                  ->orWhere('self_pr', 'LIKE', "%{$searchKeyword}%")
                  ->orWhereHas('projectExperiences', function ($subQ) use ($searchKeyword) {
                      $subQ->where('project_name', 'LIKE', "%{$searchKeyword}%")
                           ->orWhere('project_summary', 'LIKE', "%{$searchKeyword}%")
                           ->orWhere('tools_technologies_used', 'LIKE', "%{$searchKeyword}%");
                  });
            });
        }

        // スキルによる絞り込み (複数選択)
        if (!empty($selectedSkillIds)) {
            // 選択された全てのスキルIDを持つエンジニアを検索
            // (リレーション先のスキルIDが、選択されたスキルIDの配列に全て含まれる、という意味ではないので注意)
            // 選択されたスキルIDのいずれかを持つエンジニアを検索する場合はwhereInで良い
            // ここでは、選択されたスキルIDをAND条件のように扱う (全て持つエンジニアを探す場合)
            // $query->whereHas('skills', function ($q) use ($selectedSkillIds) {
            //     $q->whereIn('skills.id', $selectedSkillIds);
            // }, '=', count($selectedSkillIds));
            //
            // OR条件（いずれかのスキルを持つ）で絞り込む場合：
            $query->whereHas('skills', function ($q) use ($selectedSkillIds) {
                 $q->whereIn('skills.id', $selectedSkillIds);
            });
        }

        // 経験年数による絞り込み (総実務経験年数)
        if (!empty($experienceYearsMin)) {
            $query->where('total_experience_years', '>=', $experienceYearsMin);
        }

        $engineers = $query->orderBy('name')->paginate(10); // 氏名でソート、10件ずつページネーション

        // 検索フォーム表示用に再度スキル一覧を取得
        $skillsForForm = Skill::orderBy('type')->orderBy('name')->get();

        return view('engineers.search_results', [
            'engineers' => $engineers,
            'skillsForForm' => $skillsForForm,
            'request' => $request, // 検索条件をビューに渡してフォームに再表示
        ]);
    }
}
