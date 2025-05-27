@extends('layouts.app')

@section('title', 'エンジニア検索結果')

@section('content')
<div class="container mt-4">
    {{-- 検索オプション --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">検索オプション</h4>
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#searchModal">
                        {{-- ハンバーガーメニューアイコン --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                        </svg>
                        検索条件を編集
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 検索フォーム用モーダル --}}
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route(Route::currentRouteName() ?: 'engineers.search') }}" method="GET">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchModalLabel">検索条件</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('engineers.partials._search_form_fields', ['availableSkills' => $availableSkills, 'currentInputs' => $currentInputs])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                        <a href="{{ route('engineers.searchForm') }}" class="btn btn-outline-info">条件をリセットして新規検索</a>
                        <button type="submit" class="btn btn-primary">この条件で再検索</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 検索結果表示エリア --}}
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($engineers as $engineer)
                                    <tr>
                                        <td>{{ $engineer->name }}</td>
                                        <td>
                                            @foreach($engineer->skills->take(3) as $skill)
                                                <span class="badge bg-info text-dark me-1">{{ $skill->name }} ({{ $skill->pivot->experience_years ?? 'N/A' }}年)</span>
                                            @endforeach
                                            @if($engineer->skills->count() > 3)
                                                <span class="badge bg-secondary text-white">他</span>
                                            @endif
                                        </td>
                                        <td>{{ $engineer->total_experience_years ?? 'N/A' }} 年</td>
                                        <td>{{ $engineer->desired_salary_min ? number_format($engineer->desired_salary_min) . ' 円' : 'N/A' }}</td>
                                        <td>{{ $engineer->availability_start_date ? \Carbon\Carbon::parse($engineer->availability_start_date)->format('Y年m月d日') : 'N/A' }}</td>
                                        <td>{{ Str::limit($engineer->self_pr, 50) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
