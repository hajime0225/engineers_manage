<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engineer extends Model
{
    use HasFactory;

    // ----------------------------------------------------------------------
    // DB処理
    // ----------------------------------------------------------------------
    public function skills()
    {
        // engineersテーブルとskillsテーブルを中間テーブルengineer_skillsを介して多対多の関係で結びつける
        // withPivotで中間テーブルのカラムも取得できるようにする
        return $this->belongsToMany(Skill::class, 'engineer_skills')
                    ->withPivot('experience_years', 'proficiency_level')
                    ->withTimestamps();
    }

    public function projectExperiences()
    {
        // engineersテーブルとproject_experiencesテーブルを1対多の関係で結びつける
        return $this->hasMany(ProjectExperience::class);
    }

    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'engineer_qualifications')
                    ->withPivot('acquisition_date', 'attempts')
                    ->withTimestamps();
    }

    public function languageSkills()
    {
        return $this->hasMany(LanguageSkill::class);
    }

    // ----------------------------------------------------------------------
    // コンバート処理
    // ----------------------------------------------------------------------
    /**
     * 年代の表示用文字列を取得します。
     * 例: '30s_late' -> '30代後半'
     *
     * @return string
     */
    public function getDisplayAgeGroupAttribute(): string
    {
        if (empty($this->age_group)) {
            return '未設定';
        }

        // データベースの値 (例: "30s_late") をアンダースコアで分割
        $parts = explode('_', $this->age_group);

        // "30s" と "late" のように2つの部分に分かれることを期待
        if (count($parts) === 2) {
            $decadeIdentifier = $parts[0]; // 例: "30s"
            $suffixKey = $parts[1];        // 例: "late"

            $decadeNumber = str_replace('s', '', $decadeIdentifier);

            if (is_numeric($decadeNumber)) {
                // config ファイルから接尾辞の表示文字列を取得
                // config('display_mappings.age_group_suffix.late') のようになる
                $suffixDisplay = config('display_mappings.age_group_suffix.' . $suffixKey);

                // 接尾辞の表示文字列が見つかれば、結合して返す
                if ($suffixDisplay) {
                    return $decadeNumber . '代' . $suffixDisplay; // 例: "30" + "代" + "後半" -> "30代後半"
                }
            }
        }

        // 上記の処理でうまく変換できなかった場合は、元の値をそのまま返すか、
        // より汎用的なフォールバック処理をここに追加することも可能です。
        // 例: return $this->age_group; // 元の値 '30s_late' をそのまま返す
        // ここでは、変換できなかった場合も元の値を返すようにしています。
        // もしくは、元の値を少し整形して返すことも考えられます。
        // 例えば、'30s late' のようにアンダースコアをスペースに置換するなど。
        // return str_replace('_', ' ', $this->age_group);

        // 今回は、変換できなかった場合は元の値を返すようにします。
        return $this->age_group ?? '未設定';
    }

    /**
     * 性別の表示用文字列を取得します。
     *
     * @return string
     */
    public function getDisplayGenderAttribute(): string
    {
        return config('display_mappings.gender.' . $this->gender, $this->gender ?? '未設定');
    }

    /**
     * 単価タイプの表示用文字列を取得します。
     *
     * @return string
     */
    public function getDisplaySalaryTypeAttribute(): string
    {
        return config('display_mappings.salary_type.' . $this->salary_type, $this->salary_type ?? 'N/A');
    }

    /**
     * 稼働率の表示用文字列を取得します。
     *
     * @return string
     */
    public function getDisplayWorkCommitmentRateAttribute(): string
    {
        return config('display_mappings.work_commitment_rate.' . $this->work_commitment_rate, $this->work_commitment_rate ?? '未設定');
    }
}
