{{-- resources/views/engineers/partials/_search_form_fields.blade.php --}}

{{-- キーワード検索 --}}
<div class="mb-3">
    <label for="keyword" class="form-label">キーワード</label>
    <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $currentInputs['keyword'] ?? old('keyword', '') }}" placeholder="氏名、スキル、プロジェクト概要など">
</div>

{{-- スキル検索 (複数選択チェックボックス) --}}
<div class="mb-3">
    <label class="form-label">スキル</label>
    <div class="row">
        @php
            $skillTypes = $availableSkills->groupBy('type');
            $selectedSkills = $currentInputs['skills'] ?? old('skills', []);
            if (!is_array($selectedSkills)) $selectedSkills = []; // 配列であることを保証
        @endphp
        @foreach($skillTypes as $type => $skillsOfType)
        <div class="col-md-3 mb-2">
            <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong>
            @foreach($skillsOfType as $skill)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="skills[]" value="{{ $skill->id }}" id="skill_partial_{{ $skill->id }}"
                    {{ in_array($skill->id, $selectedSkills) ? 'checked' : '' }}>
                <label class="form-check-label" for="skill_partial_{{ $skill->id }}">
                    {{ $skill->name }}
                </label>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

{{-- 経験年数 (下限) --}}
<div class="mb-3">
    <label for="experience_years_min" class="form-label">総実務経験年数 (下限)</label>
    <input type="number" class="form-control" id="experience_years_min" name="experience_years_min" value="{{ $currentInputs['experience_years_min'] ?? old('experience_years_min', '') }}" min="0" placeholder="例: 3 (年以上)">
</div>
