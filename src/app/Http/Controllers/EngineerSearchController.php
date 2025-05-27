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
    public function showSearchForm(Request $request) // Request を受け取るように変更
    {
        $skills = Skill::orderBy('type')->orderBy('name')->get();
        return view('engineers.search_form', [
            'availableSkills' => $skills,
            'currentInputs' => $request->all() // 現在のリクエストパラメータを渡す
        ]);
    }

    /**
     * エンジニアを検索し、結果を表示します。
     */
    public function searchEngineers(Request $request)
    {
        $query = Engineer::query()->with(['skills', 'projectExperiences']);

        $searchKeyword = $request->input('keyword');
        $selectedSkillIds = $request->input('skills', []);
        $experienceYearsMin = $request->input('experience_years_min');

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

        if (!empty($selectedSkillIds) && is_array($selectedSkillIds)) { // 配列であることも確認
            $query->whereHas('skills', function ($q) use ($selectedSkillIds) {
                 $q->whereIn('skills.id', $selectedSkillIds);
            });
        }

        if (!empty($experienceYearsMin)) {
            $query->where('total_experience_years', '>=', $experienceYearsMin);
        }

        $engineers = $query->orderBy('name')->paginate(10);
        $skillsForForm = Skill::orderBy('type')->orderBy('name')->get();

        return view('engineers.search_results', [
            'engineers' => $engineers,
            'availableSkills' => $skillsForForm,
            'currentInputs' => $request->all(),
            'request' => $request,
        ]);
    }
}
