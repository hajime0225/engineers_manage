@extends('layouts.app')

@section('title', 'エンジニア検索結果')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>エンジニア再検索</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('engineers.show') }}" method="GET">
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $request->input('keyword') }}" placeholder="氏名、スキル、プロジェクト概要など">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">スキル</label>
                            <div class="row">
                                @php $skillTypes = $skillsForForm->groupBy('type'); @endphp
                                @foreach($skillTypes as $type => $skillsOfType)
                                <div class="col-md-3 mb-2">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong>
                                    @foreach($skillsOfType as $skill)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="skills[]" value="{{ $skill->id }}" id="form_skill_{{ $skill->id }}"
                                            {{ (is_array($request->input('skills')) && in_array($skill->id, $request->input('skills'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="form_skill_{{ $skill->id }}">
                                            {{ $skill->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="experience_years_min" class="form-label">総実務経験年数 (下限)</label>
                            <input type="number" class="form-control" id="experience_years_min" name="experience_years_min" value="{{ $request->input('experience_years_min') }}" min="0" placeholder="例: 3 (年以上)">
                        </div>
                        <button type="submit" class="btn btn-primary">再検索</button>
                        <a href="{{ route('engineers.searchForm') }}" class="btn btn-secondary">条件クリア</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 検索結果 --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>検索結果 ({{ $engineers->total() }}件)</h4>
                </div>
                <div class="card-body">
                    @if($engineers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>氏名</th>
                                        <th>主要スキル</th>
                                        <th>総実務経験年数</th>
                                        <th>希望単価(下限)</th>
                                        <th>稼働開始時期</th>
                                        <th>自己PR (一部)</th>
                                        {{-- <th>詳細</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($engineers as $engineer)
                                    <tr>
                                        <td>{{ $engineer->name }}</td>
                                        <td>
                                            @foreach($engineer->skills->take(3) as $skill) {{-- 主要スキルを3つまで表示 --}}
                                                <span class="badge bg-info text-dark me-1">{{ $skill->name }} ({{ $skill->pivot->experience_years ?? 'N/A' }}年)</span>
                                            @endforeach
                                            @if($engineer->skills->count() > 3)
                                                <span class="badge bg-secondary text-white">他</span>
                                            @endif
                                        </td>
                                        <td>{{ $engineer->total_experience_years ?? 'N/A' }} 年</td>
                                        <td>{{ number_format($engineer->desired_salary_min) ?? 'N/A' }} 円</td>
                                        <td>{{ $engineer->availability_start_date ? \Carbon\Carbon::parse($engineer->availability_start_date)->format('Y年m月d日') : 'N/A' }}</td>
                                        <td>{{ Str::limit($engineer->self_pr, 50) }}</td> {{-- 自己PRを50文字まで表示 --}}
                                        {{-- 詳細表示ページへのリンクは後ほど作成 --}}
                                        {{-- <td><a href="#" class="btn btn-sm btn-outline-primary">詳細</a></td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- ページネーションリンク --}}
                        <div class="d-flex justify-content-center">
                            {{ $engineers->appends($request->except('page'))->links() }}
                        </div>
                    @else
                        <p class="text-center">該当するエンジニアは見つかりませんでした。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
