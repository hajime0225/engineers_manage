@extends('layouts.admin.app')

@section('title', 'マスターデータ管理')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">マスターデータ管理</h4>
                    <!-- 編集制御ボタン -->
                    <div id="edit-controls">
                        <button type="button" class="btn btn-primary" id="edit-btn">編集</button>
                        <button type="button" class="btn btn-secondary d-none" id="cancel-btn">編集終了</button>
                        <button type="button" class="btn btn-success d-none" id="update-btn">更新</button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- エラーメッセージ表示 -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 成功メッセージ表示 -->
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- エラーメッセージ表示 -->
                    @if (session('error'))
                        <div class="alert alert-danger">{!! session('error') !!}</div>
                    @endif

                    <!-- 情報メッセージ表示 -->
                    @if (session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <!-- タブナビゲーション -->
                    <ul class="nav nav-tabs" id="masterDataTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'skills' ? 'active' : '' }}"
                                    id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills"
                                    type="button" role="tab" aria-controls="skills"
                                    aria-selected="{{ $activeTab === 'skills' ? 'true' : 'false' }}">
                                スキル管理
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'qualifications' ? 'active' : '' }}"
                                    id="qualifications-tab" data-bs-toggle="tab" data-bs-target="#qualifications"
                                    type="button" role="tab" aria-controls="qualifications"
                                    aria-selected="{{ $activeTab === 'qualifications' ? 'true' : 'false' }}">
                                資格管理
                            </button>
                        </li>
                    </ul>

                    <!-- タブコンテンツ -->
                    <div class="tab-content" id="masterDataTabsContent">
                        <!-- スキル管理タブ -->
                        <div class="tab-pane fade {{ $activeTab === 'skills' ? 'show active' : '' }}"
                             id="skills" role="tabpanel" aria-labelledby="skills-tab">
                            <div class="mt-3">

                                {{-- 参照モード用の表示 --}}
                                <div id="skills-read-mode">
                                    @if($activeTab === 'skills')
                                        @include('admin.partials._read_skills', ['items' => $items, 'skillTypes' => $skillTypes ?? []])
                                    @endif
                                </div>

                                {{-- 更新モード用のフォーム --}}
                                <div id="skills-edit-mode" style="display: none;">
                                    <form id="skills-form" method="POST" action="{{ route('admin.update') }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="master_type" value="skills">

                                        @if($activeTab === 'skills')
                                            @include('admin.partials._update_skills', ['items' => $items, 'skillTypes' => $skillTypes ?? []])
                                        @endif
                                    </form>
                                </div>

                            </div>
                        </div>

                        <!-- 資格管理タブ -->
                        <div class="tab-pane fade {{ $activeTab === 'qualifications' ? 'show active' : '' }}"
                             id="qualifications" role="tabpanel" aria-labelledby="qualifications-tab">
                            <div class="mt-3">

                                {{-- 参照モード用の表示 --}}
                                <div id="qualifications-read-mode">
                                    @if($activeTab === 'qualifications')
                                        @include('admin.partials._read_qualifications', ['items' => $items])
                                    @endif
                                </div>

                                {{-- 更新モード用のフォーム --}}
                                <div id="qualifications-edit-mode" style="display: none;">
                                    <form id="qualifications-form" method="POST" action="{{ route('admin.update') }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="master_type" value="qualifications">

                                        @if($activeTab === 'qualifications')
                                            @include('admin.partials._update_qualifications', ['items' => $items])
                                        @endif
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 削除用の隠しフォーム -->
<form id="delete-form" method="POST" action="{{ route('admin.destroy') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="master_type" id="delete-master-type">
    <input type="hidden" name="id" id="delete-id">
</form>
@endsection

@push('scripts')
<!-- jQueryが必要な場合は先に読み込み -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- AjaxのURL情報をJavaScriptに渡す -->
<script>
    window.adminTabDataUrl = '{{ route("admin.tabData") }}';
</script>

<!-- 管理画面用JavaScript (public/js配置) -->
<script src="{{ asset('js/admin-manage.js') }}"></script>
@endpush
