@extends('layouts.app')

@section('title', 'エンジニア検索')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>エンジニア検索</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('engineers.show') }}" method="GET">
                        {{-- キーワード検索 --}}
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" value="{{ request('keyword') }}" placeholder="氏名、スキル、プロジェクト概要など">
                        </div>

                        {{-- スキル検索 (複数選択チェックボックス) --}}
                        <div class="mb-3">
                            <label class="form-label">スキル</label>
                            <div class="row">
                                @php $skillTypes = $skills->groupBy('type'); @endphp
                                @foreach($skillTypes as $type => $skillsOfType)
                                <div class="col-md-3 mb-2">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong>
                                    @foreach($skillsOfType as $skill)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="skills[]" value="{{ $skill->id }}" id="skill_{{ $skill->id }}"
                                            {{ (is_array(request('skills')) && in_array($skill->id, request('skills'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="skill_{{ $skill->id }}">
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
                            <input type="number" class="form-control" id="experience_years_min" name="experience_years_min" value="{{ request('experience_years_min') }}" min="0" placeholder="例: 3 (年以上)">
                        </div>

                        <button type="submit" class="btn btn-primary">検索する</button>
                        <a href="{{ route('engineers.searchForm') }}" class="btn btn-secondary">リセット</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
