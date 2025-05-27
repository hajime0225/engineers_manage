@extends('layouts.app')

@section('title', $engineer->name . 'さんの詳細情報')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $engineer->name }}さんの詳細情報</h4>
                <a href="{{ url()->previous(route('engineers.show')) }}" class="btn btn-outline-secondary btn-sm">一覧へ戻る</a>
            </div>
        </div>
        <div class="card-body">
            {{-- 基本情報セクション --}}
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-person-lines-fill me-2" viewBox="0 0 16 16"><path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1z"/></svg>基本情報</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 25%;">エンジニアID</th>
                            <td>{{ $engineer->id }}</td>
                        </tr>
                        <tr>
                            <th>氏名</th>
                            <td>{{ $engineer->name }}</td>
                        </tr>
                        <tr>
                            <th>年代</th>
                            <td>{{ $engineer->display_age_group }}</td>
                        </tr>
                        <tr>
                            <th>性別</th>
                            <td>{{ $engineer->display_gender }}</td>
                        </tr>
                        <tr>
                            <th>総実務経験年数</th>
                            <td>{{ $engineer->total_experience_years ?? 'N/A' }} 年</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 稼働条件・希望セクション --}}
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-calendar-check me-2" viewBox="0 0 16 16"><path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>稼働条件・希望</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 25%;">希望単価</th>
                            <td>
                                @if(isset($engineer->desired_salary_min) && isset($engineer->desired_salary_max))
                                    {{ number_format($engineer->desired_salary_min) }} 円 〜 {{ number_format($engineer->desired_salary_max) }} 円
                                @elseif(isset($engineer->desired_salary_min))
                                    {{ number_format($engineer->desired_salary_min) }} 円 〜
                                @elseif(isset($engineer->desired_salary_max))
                                    〜 {{ number_format($engineer->desired_salary_max) }} 円
                                @else
                                    未設定
                                @endif
                                ({{ $engineer->display_salary_type }})
                            </td>
                        </tr>
                        <tr>
                            <th>稼働開始可能時期</th>
                            <td>{{ $engineer->availability_start_date ? \Carbon\Carbon::parse($engineer->availability_start_date)->format('Y年m月d日') : '未設定' }}</td>
                        </tr>
                        <tr>
                            <th>希望勤務地</th>
                            <td>{{ $engineer->desired_work_location ?? '未設定' }}</td>
                        </tr>
                        <tr>
                            <th>稼働率</th>
                            <td>{{ $engineer->display_work_commitment_rate }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 自己PR・強み・推薦コメントセクション --}}
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-chat-left-text-fill me-2" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"/></svg>自己PR・強み・推薦</h5>
                @if($engineer->self_pr)
                    <div class="mb-2">
                        <strong>自己PR/強み:</strong>
                        <p style="white-space: pre-line;">{{ $engineer->self_pr }}</p>
                    </div>
                @endif
                @if($engineer->agent_recommendation)
                    <div class="mb-2">
                        <strong>派遣事業者からの推薦コメント:</strong>
                        <p style="white-space: pre-line;">{{ $engineer->agent_recommendation }}</p>
                    </div>
                @endif
                @if($engineer->portfolio_url)
                    <div class="mb-2">
                        <strong>ポートフォリオ/実績公開URL:</strong>
                        <p><a href="{{ $engineer->portfolio_url }}" target="_blank" rel="noopener noreferrer">{{ $engineer->portfolio_url }}</a></p>
                    </div>
                @endif
                @if(!$engineer->self_pr && !$engineer->agent_recommendation && !$engineer->portfolio_url)
                    <p>情報がありません。</p>
                @endif
            </div>

            {{-- 保有スキルセクション --}}
            @if($engineer->skills->isNotEmpty())
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-tools me-2" viewBox="0 0 16 16"><path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.1 3.1 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.356 3.356a1 1 0 0 0 1.414 0l1.586-1.586a1 1 0 0 0 0-1.414l-3.356-3.356a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96A3.1 3.1 0 0 0 9 3.5a3 3 0 1 0-5.878.851L.851 1.149ZM15 1.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5M14.293.293a1 1 0 0 1 1.414 0l.001.001L16 1l-.207.207a1 1 0 0 1-1.414 0l-1.586-1.586a1 1 0 0 1 0-1.414l.207-.207L14.293.293Z"/></svg>保有スキル</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>スキル名</th>
                            <th>種別</th>
                            <th>経験年数</th>
                            <th>習熟度</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($engineer->skills as $skill)
                        <tr>
                            <td>{{ $skill->name }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $skill->type)) }}</td>
                            <td>{{ $skill->pivot->experience_years ?? 'N/A' }} 年</td>
                            <td>{{ $skill->pivot->proficiency_level ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- 職務経歴セクション --}}
            @if($engineer->projectExperiences->isNotEmpty())
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-briefcase-fill me-2" viewBox="0 0 16 16"><path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zM0 8v4.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V8l-7.528 2.259a.5.5 0 0 1-.444 0z"/></svg>職務経歴</h5>
                @foreach($engineer->projectExperiences as $index => $experience)
                <div class="card mb-3">
                    <div class="card-header">
                        プロジェクト {{ $index + 1 }}: {{ $experience->project_name ?? 'N/A' }}
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">期間</dt>
                            <dd class="col-sm-9">
                                {{ $experience->start_date ? \Carbon\Carbon::parse($experience->start_date)->format('Y年m月') : 'N/A' }}
                                〜
                                {{ $experience->end_date ? \Carbon\Carbon::parse($experience->end_date)->format('Y年m月') : '現在' }}
                            </dd>
                            <dt class="col-sm-3">業界</dt>
                            <dd class="col-sm-9">{{ $experience->industry ?? 'N/A' }}</dd>
                            <dt class="col-sm-3">役割</dt>
                            <dd class="col-sm-9">{{ $experience->roles ?? 'N/A' }}</dd>
                            <dt class="col-sm-3">担当フェーズ</dt>
                            <dd class="col-sm-9">{{ $experience->phases ?? 'N/A' }}</dd>
                            <dt class="col-sm-3">概要</dt>
                            <dd class="col-sm-9" style="white-space: pre-line;">{{ $experience->project_summary ?? 'N/A' }}</dd>
                            <dt class="col-sm-3">使用ツール・技術</dt>
                            <dd class="col-sm-9" style="white-space: pre-line;">{{ $experience->tools_technologies_used ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- 保有資格セクション --}}
            @if($engineer->qualifications->isNotEmpty())
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-patch-check-fill me-2" viewBox="0 0 16 16"><path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89.01-.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89-.01zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708"/></svg>保有資格</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>資格名</th>
                            <th>取得日</th>
                            <th>受験回数</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($engineer->qualifications as $qualification)
                        <tr>
                            <td>{{ $qualification->name }}</td>
                            <td>{{ $qualification->pivot->acquisition_date ? \Carbon\Carbon::parse($qualification->pivot->acquisition_date)->format('Y年m月d日') : 'N/A' }}</td>
                            <td>{{ $qualification->pivot->attempts ?? 'N/A' }} 回</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- 外国語スキルセクション --}}
            @if($engineer->languageSkills->isNotEmpty())
            <div class="mb-4">
                <h5><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-translate me-2" viewBox="0 0 16 16"><path d="M4.545 6.714 4.11 8H3l1.862-5h1.284L8 8H6.833l-.435-1.286zm1.634-2.335L5.07 7.232h1.22L7.35 4.379zM10.088 5h-1.2V4h1.2zm-1.2 2h1.2V6h-1.2zm.32-5a.5.5 0 0 0-.5.5v1.5a.5.5 0 0 0 1 0V2.5a.5.5 0 0 0-.5-.5m-2.75 5h-1.2V4h1.2zm-1.2 2h1.2V6h-1.2zm.32-5a.5.5 0 0 0-.5.5v1.5a.5.5 0 0 0 1 0V2.5a.5.5 0 0 0-.5-.5M16 3.5a.5.5 0 0 1-.5.5H14a.5.5 0 0 1 0-1h1.5a.5.5 0 0 1 .5.5M13 10H3V9h10zm0-1H3V8h10zm0-1H3V7h10zm.5-2a.5.5 0 0 0-.5-.5h-13a.5.5 0 0 0 0 1h13a.5.5 0 0 0 .5-.5M2 5h12V4H2zm0 8h12V9H2zm1 0h10V8H3zm1-1h8V7H4zm1-1h6V6H5zm1-1h4V5H6z"/></svg>外国語スキル</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>言語名</th>
                            <th>習熟度</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($engineer->languageSkills as $langSkill)
                        <tr>
                            <td>{{ $langSkill->language_name }}</td>
                            <td>{{ $langSkill->proficiency_level }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
