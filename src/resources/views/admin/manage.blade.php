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
                                <form id="skills-form" method="POST" action="{{ route('admin.update') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="master_type" value="skills">

                                    @if($activeTab === 'skills' && $items->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>スキル名</th>
                                                        <th>スキル種別</th>
                                                        <th>作成日</th>
                                                        <th>更新日</th>
                                                        <th>操作</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $index => $item)
                                                    <tr data-id="{{ $item->id }}">
                                                        <td>
                                                            {{ $item->id }}
                                                            <input type="hidden" name="records[{{ $index }}][id]" value="{{ $item->id }}">
                                                        </td>
                                                        <td>
                                                            <span class="view-mode">{{ $item->name }}</span>
                                                            <input type="text" class="form-control edit-mode d-none"
                                                                   name="records[{{ $index }}][name]"
                                                                   value="{{ old('records.'.$index.'.name', $item->name) }}"
                                                                   data-original="{{ $item->name }}">
                                                        </td>
                                                        <td>
                                                            <span class="view-mode">{{ $skillTypes[$item->type] ?? $item->type }}</span>
                                                            <select class="form-control edit-mode d-none"
                                                                    name="records[{{ $index }}][type]"
                                                                    data-original="{{ $item->type }}">
                                                                @foreach($skillTypes as $typeKey => $typeLabel)
                                                                    <option value="{{ $typeKey }}"
                                                                            {{ (old('records.'.$index.'.type', $item->type) === $typeKey) ? 'selected' : '' }}>
                                                                        {{ $typeLabel }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>{{ $item->updated_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <form method="POST" action="{{ route('admin.destroy') }}" class="d-inline"
                                                                  onsubmit="return confirm('本当に削除しますか？')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="master_type" value="skills">
                                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                                <button type="submit" class="btn btn-danger btn-sm">削除</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center mt-3">スキルデータがありません。</p>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- 資格管理タブ -->
                        <div class="tab-pane fade {{ $activeTab === 'qualifications' ? 'show active' : '' }}"
                             id="qualifications" role="tabpanel" aria-labelledby="qualifications-tab">
                            <div class="mt-3">
                                <form id="qualifications-form" method="POST" action="{{ route('admin.update') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="master_type" value="qualifications">

                                    @if($activeTab === 'qualifications' && $items->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>資格名</th>
                                                        <th>作成日</th>
                                                        <th>更新日</th>
                                                        <th>操作</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $index => $item)
                                                    <tr data-id="{{ $item->id }}">
                                                        <td>
                                                            {{ $item->id }}
                                                            <input type="hidden" name="records[{{ $index }}][id]" value="{{ $item->id }}">
                                                        </td>
                                                        <td>
                                                            <span class="view-mode">{{ $item->name }}</span>
                                                            <input type="text" class="form-control edit-mode d-none"
                                                                   name="records[{{ $index }}][name]"
                                                                   value="{{ old('records.'.$index.'.name', $item->name) }}"
                                                                   data-original="{{ $item->name }}">
                                                        </td>
                                                        <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>{{ $item->updated_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <form method="POST" action="{{ route('admin.destroy') }}" class="d-inline"
                                                                  onsubmit="return confirm('本当に削除しますか？')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="master_type" value="qualifications">
                                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                                <button type="submit" class="btn btn-danger btn-sm">削除</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center mt-3">資格データがありません。</p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const updateBtn = document.getElementById('update-btn');

    // 編集モード開始
    editBtn.addEventListener('click', function() {
        toggleEditMode(true);
        toggleButtons(true);
    });

    // 編集キャンセル
    cancelBtn.addEventListener('click', function() {
        // 元の値に戻す
        resetFormValues();
        toggleEditMode(false);
        toggleButtons(false);
    });

    // 更新処理
    updateBtn.addEventListener('click', function() {
        // アクティブなタブのフォームを送信
        const activeTab = document.querySelector('.tab-pane.show.active');
        if (activeTab) {
            const form = activeTab.querySelector('form');
            if (form) {
                form.submit();
            }
        }
    });

    // タブ切り替え時の処理
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // 編集モードを終了
            resetFormValues();
            toggleEditMode(false);
            toggleButtons(false);

            // URLを更新
            const tabName = this.getAttribute('data-bs-target').replace('#', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        });
    });

    function toggleEditMode(isEdit) {
        const viewElements = document.querySelectorAll('.view-mode');
        const editElements = document.querySelectorAll('.edit-mode');

        viewElements.forEach(el => {
            if (isEdit) {
                el.classList.add('d-none');
            } else {
                el.classList.remove('d-none');
            }
        });

        editElements.forEach(el => {
            if (isEdit) {
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });
    }

    function toggleButtons(isEdit) {
        if (isEdit) {
            editBtn.classList.add('d-none');
            cancelBtn.classList.remove('d-none');
            updateBtn.classList.remove('d-none');
        } else {
            editBtn.classList.remove('d-none');
            cancelBtn.classList.add('d-none');
            updateBtn.classList.add('d-none');
        }
    }

    function resetFormValues() {
        const editInputs = document.querySelectorAll('.edit-mode');
        editInputs.forEach(input => {
            const originalValue = input.getAttribute('data-original');
            if (originalValue !== null) {
                if (input.tagName === 'SELECT') {
                    input.value = originalValue;
                } else {
                    input.value = originalValue;
                }
            }
        });
    }
});
</script>

@endsection
