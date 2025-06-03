<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Qualification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AdminMasterDataController extends Controller
{
    /**
     * マスターデータ登録フォーム（タブ形式）を表示します。
     */
    public function create()
    {
        // Skill登録フォーム用のスキル種別 (AdminSkillControllerから流用)
        $skillTypes = [
            'programming_language' => 'プログラミング言語',
            'framework' => 'フレームワーク/ライブラリ',
            'database' => 'データベース',
            'os_cloud' => 'OS/クラウド環境',
            'tool' => 'ツール',
            'other' => 'その他',
        ];
        return view('admin.create', compact('skillTypes'));
    }

    /**
     * 送信されたマスターデータを保存します。
     */
    public function store(Request $request)
    {
        $masterType = $request->input('master_type');
        $redirectRoute = 'admin.create'; // 保存後のリダイレクト先
        $successMessage = '';

        if ($masterType === 'skills') {
            $validatedData = $request->validate([
                'skills_name' => [
                    'required', 'string', 'max:100',
                    Rule::unique('skills', 'name')->where(function ($query) use ($request) {
                        return $query->where('name', $request->input('skills_name'));
                    })
                ],
                'skills_type' => 'required|string|max:50',
            ], [
                'skills_name.required' => '[スキル] スキル名は必須です。',
                'skills_name.unique' => '[スキル] 同じスキル名が既に登録されています。',
                'skills_type.required' => '[スキル] スキル種別は必須です。',
            ]);
            Skill::create([
                'name' => $validatedData['skills_name'],
                'type' => $validatedData['skills_type']
            ]);
            $successMessage = 'スキルが正常に登録されました。';

        } elseif ($masterType === 'qualifications') {
            $validatedData = $request->validate([
                'qualifications_name' => 'required|string|max:255|unique:qualifications,name',
            ], [ /* カスタムメッセージ */
                'qualifications_name.required' => '[資格] 資格名は必須です。',
                'qualifications_name.unique' => '[資格] 同じ資格名が既に登録されています。',
            ]);
            Qualification::create(['name' => $request->qualifications_name]);
            $successMessage = '資格が正常に登録されました。';

        } else {
            return redirect()->route($redirectRoute)->with('error', '無効なマスター種別です。');
        }

        return redirect()->route($redirectRoute)->with('success', $successMessage);
    }

    /**
     * Ajax用：タブ切り替え時にデータを取得してHTMLを返す
     */
    public function getTabData(Request $request)
    {
        $activeTab = $request->query('tab', 'skills');
        $items = collect();
        $viewData = [];

        if ($activeTab === 'skills') {
            $items = Skill::orderBy('type')->orderBy('name')->get();
            $viewData['skillTypes'] = [
                'programming_language' => 'プログラミング言語',
                'framework' => 'フレームワーク/ライブラリ',
                'database' => 'データベース',
                'os_cloud' => 'OS/クラウド環境',
                'tool' => 'ツール',
                'other' => 'その他',
            ];
        } elseif ($activeTab === 'qualifications') {
            $items = Qualification::orderBy('name')->get();
            $viewData['skillTypes'] = [];
        }

        // 共通データ
        $viewData['items'] = $items;

        try {
            return response()->json([
                'success' => true,
                'activeTab' => $activeTab,
                'readModeHtml' => view('admin.partials._read_' . $activeTab, $viewData)->render()
            ]);
        } catch (\Exception $e) {
            // エラーログを出力
            \Log::error('Ajax getTabData error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * マスターデータ参照・編集・削除画面（タブと一覧）を表示します。
     * /admin/edit 用
     */
    public function edit(Request $request)
    {
        $activeTab = $request->query('tab', 'skills');
        $items = collect();
        $viewData = [];

        if ($activeTab === 'skills') {
            $items = Skill::orderBy('type')->orderBy('name')->get();
            $viewData['skillTypes'] = [
                'programming_language' => 'プログラミング言語',
                'framework' => 'フレームワーク/ライブラリ',
                'database' => 'データベース',
                'os_cloud' => 'OS/クラウド環境',
                'tool' => 'ツール',
                'other' => 'その他',
            ];
        } elseif ($activeTab === 'qualifications') {
            $items = Qualification::orderBy('name')->get();
        } else {
            $activeTab = 'skills';
            $items = Skill::orderBy('type')->orderBy('name')->get();
            $viewData['skillTypes'] = [ /* ... */ ];
        }

        return view('admin.manage', array_merge($viewData, [
            'items' => $items,
            'activeTab' => $activeTab,
        ]));
    }

    /**
     * 送信されたマスターデータを更新します。
     * /admin/edit (PATCH) 用
     */
    public function update(Request $request)
    {
        $masterType = $request->input('master_type');
        $records = $request->input('records', []);
        $successMessage = '';
        $errorMessages = [];

        if (empty($masterType)) {
            return back()->with('error', 'マスター種別が指定されていません。');
        }

        if (empty($records)) {
            return redirect()->route('admin.edit', ['tab' => $masterType])->with('info', '更新するデータがありませんでした。');
        }

        foreach ($records as $index => $recordData) {
            if (!isset($recordData['id'])) continue;

            $id = $recordData['id'];
            $validator = null;

            if ($masterType === 'skills') {
                $skill = Skill::find($id);
                if (!$skill) {
                    $errorMessages[] = "ID:{$id} のスキルが見つかりません。";
                    continue;
                }
                // スキル用のバリデーションルール
                $validator = Validator::make($recordData, [
                    'name' => ['required', 'string', 'max:100', Rule::unique('skills')->where(function ($query) use ($recordData, $skill) {
                        return $query->where('type', $recordData['type'] ?? $skill->type);
                    })->ignore($skill->id)],
                    'type' => ['required', 'string', 'max:50'],
                ], [
                    'name.required' => "ID:{$id} [スキル] スキル名は必須です。",
                    'name.unique' => "ID:{$id} [スキル] そのスキル種別・スキル名の組み合わせは既に登録されています。",
                    'type.required' => "ID:{$id} [スキル] スキル種別は必須です。",
                ]);

                if ($validator->fails()) {
                    return redirect()->route('admin.edit', ['tab' => $masterType])
                                    ->withErrors($validator)
                                    ->withInput($request->all());
                }
                $skill->update($validator->validated());

            } elseif ($masterType === 'qualifications') {
                $qualification = Qualification::find($id);
                if (!$qualification) {
                    $errorMessages[] = "ID:{$id} の資格が見つかりません。";
                    continue;
                }
                // 資格用のバリデーションルール
                $validator = Validator::make($recordData, [
                    'name' => ['required', 'string', 'max:255', Rule::unique('qualifications')->ignore($qualification->id)],
                ], [
                    'name.required' => "ID:{$id} [資格] 資格名は必須です。",
                    'name.unique' => "ID:{$id} [資格] 同じ資格名は既に登録されています。",
                ]);

                if ($validator->fails()) {
                    return redirect()->route('admin.edit', ['tab' => $masterType])
                                    ->withErrors($validator)
                                    ->withInput($request->all());
                }
                $qualification->update($validator->validated());
            } else {
                $errorMessages[] = "無効なマスター種別です: {$masterType}";
                break;
            }
        }

        if (!empty($errorMessages)) {
            return redirect()->route('admin.edit', ['tab' => $masterType])
                            ->with('error', implode('<br>', $errorMessages))
                            ->withInput($request->all());
        }

        // 成功時のメッセージを調整
        $typeLabel = ($masterType === 'skills') ? 'スキル' : '資格';
        $successMessage = $typeLabel . '情報が正常に更新されました。';
        return redirect()->route('admin.edit', ['tab' => $masterType])->with('success', $successMessage);
    }

    /**
     * 指定されたマスターデータを削除します。
     * /admin/destroy (DELETE) 用
     */
    public function destroy(Request $request)
    {
        $masterType = $request->input('master_type');
        $id = $request->input('id');
        $successMessage = '';

        if (empty($masterType) || empty($id)) {
            return back()->with('error', '削除対象の指定が無効です。');
        }

        if ($masterType === 'skills') {
            $skill = Skill::find($id);
            if ($skill) {
                $skill->delete();
                $successMessage = 'スキルが正常に削除されました。';
            } else {
                return back()->with('error', '削除対象のスキルが見つかりません。');
            }
        } elseif ($masterType === 'qualifications') {
            $qualification = Qualification::find($id);
            if ($qualification) {
                $qualification->delete();
                $successMessage = '資格が正常に削除されました。';
            } else {
                return back()->with('error', '削除対象の資格が見つかりません。');
            }
        } else {
            return back()->with('error', '無効なマスター種別です。');
        }

        return redirect()->route('admin.edit', ['tab' => $masterType])->with('success', $successMessage);
    }
}
