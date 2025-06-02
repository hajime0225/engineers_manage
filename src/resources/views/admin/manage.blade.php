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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // 1. DOM要素の取得
    // ========================================
    const editBtn = document.getElementById('edit-btn');           // 編集開始ボタン
    const cancelBtn = document.getElementById('cancel-btn');       // 編集キャンセルボタン
    const updateBtn = document.getElementById('update-btn');       // 更新実行ボタン

    // ========================================
    // 2. 編集モード開始の処理
    // ========================================
    editBtn.addEventListener('click', function() {
        console.log('編集モード開始'); // デバッグ用
        toggleEditMode(true);    // 参照モードから編集モードに切り替え
        toggleButtons(true);     // ボタン表示を編集モード用に切り替える
    });

    // ========================================
    // 3. 編集キャンセルの処理
    // ========================================
    cancelBtn.addEventListener('click', function() {
        console.log('編集キャンセル'); // デバッグ用
        resetFormValues();       // 入力値を元の値に戻す
        toggleEditMode(false);   // 編集モードから参照モードに戻す
        toggleButtons(false);    // ボタン表示を通常モードに戻す
    });

    // ========================================
    // 4. 更新処理の実行
    // ========================================
    updateBtn.addEventListener('click', function() {
        console.log('更新処理開始'); // デバッグ用

        // 現在アクティブなタブ（表示中のタブ）を取得
        const activeTab = document.querySelector('.tab-pane.show.active');
        console.log('アクティブタブ:', activeTab ? activeTab.id : 'なし');

        if (activeTab) {
            // アクティブタブ内の編集モード用フォームを取得
            const editModeDiv = activeTab.querySelector('[id$="-edit-mode"]');
            if (editModeDiv) {
                const form = editModeDiv.querySelector('form');
                console.log('フォーム要素:', form);

                if (form) {
                    // フォームの内容をサーバーに送信
                    console.log('フォーム送信実行:', form.action, form.method);
                    form.submit();
                } else {
                    console.error('編集用フォームが見つかりません');
                }
            } else {
                console.error('編集モード要素が見つかりません');
            }
        } else {
            console.error('アクティブなタブが見つかりません');
        }
    });

    // ========================================
    // 5. 削除処理（イベント委譲を使用）
    // ========================================
    document.addEventListener('click', function(e) {
        // クリックされた要素が削除ボタンかどうかをチェック
        if (e.target.classList.contains('delete-btn')) {
            console.log('削除ボタンがクリックされました');

            // 確認ダイアログを表示
            if (confirm('本当に削除しますか？')) {
                // ボタンの data-* 属性から削除対象の情報を取得
                const id = e.target.getAttribute('data-id');        // 削除するレコードのID
                const type = e.target.getAttribute('data-type');    // マスター種別（skills/qualifications）

                console.log('削除対象:', { id: id, type: type });

                // 隠しフォームに削除対象の情報をセット
                document.getElementById('delete-id').value = id;                    // IDをセット
                document.getElementById('delete-master-type').value = type;         // 種別をセット

                // 削除用フォームを送信
                // この時点で {{ route('admin.destroy') }} に DELETE メソッドでデータが送信される
                console.log('削除フォーム送信実行');
                document.getElementById('delete-form').submit();
            } else {
                console.log('削除がキャンセルされました');
            }
        }
    });

    // ========================================
    // 6. タブ切り替え時の処理
    // ========================================
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('タブ切り替え:', this.getAttribute('data-bs-target'));

            // タブ切り替え時は編集モードを強制終了
            resetFormValues();       // 入力値をリセット
            toggleEditMode(false);   // 編集モード終了
            toggleButtons(false);    // ボタン表示を通常モードに戻す

            // ブラウザのURLを更新（ページリロード時に同じタブを表示するため）
            const tabName = this.getAttribute('data-bs-target').replace('#', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        });
    });

    // ========================================
    // 7. ユーティリティ関数群
    // ========================================

    /**
     * 編集モードの表示切り替え（参照⇔編集の切り替え）
     * @param {boolean} isEdit - true: 編集モード, false: 参照モード
     */
    function toggleEditMode(isEdit) {
        console.log('編集モード切り替え:', isEdit ? '編集ON' : '編集OFF');

        // 現在アクティブなタブを取得
        const activeTab = document.querySelector('.tab-pane.show.active');
        if (!activeTab) {
            console.error('アクティブなタブが見つかりません');
            return;
        }

        // 参照モード用の要素を取得
        const readModeDiv = activeTab.querySelector('[id$="-read-mode"]');
        // 編集モード用の要素を取得
        const editModeDiv = activeTab.querySelector('[id$="-edit-mode"]');

        console.log('参照モード要素:', readModeDiv);
        console.log('編集モード要素:', editModeDiv);

        if (readModeDiv && editModeDiv) {
            if (isEdit) {
                // 編集モード: 参照を非表示、編集を表示
                readModeDiv.style.display = 'none';
                editModeDiv.style.display = 'block';
            } else {
                // 参照モード: 参照を表示、編集を非表示
                readModeDiv.style.display = 'block';
                editModeDiv.style.display = 'none';
            }
        } else {
            console.error('参照モードまたは編集モードの要素が見つかりません');
        }
    }

    /**
     * ボタン表示の切り替え
     * @param {boolean} isEdit - true: 編集モード, false: 表示モード
     */
    function toggleButtons(isEdit) {
        console.log('ボタン表示切り替え:', isEdit ? '編集中' : '通常');

        if (isEdit) {
            // 編集モード: 編集ボタンを隠し、キャンセル・更新ボタンを表示
            editBtn.classList.add('d-none');
            cancelBtn.classList.remove('d-none');
            updateBtn.classList.remove('d-none');
        } else {
            // 通常モード: 編集ボタンを表示し、キャンセル・更新ボタンを隠す
            editBtn.classList.remove('d-none');
            cancelBtn.classList.add('d-none');
            updateBtn.classList.add('d-none');
        }
    }

    /**
     * フォームの入力値を元の値に戻す
     */
    function resetFormValues() {
        console.log('入力値をリセット中...');

        // 現在アクティブなタブを取得
        const activeTab = document.querySelector('.tab-pane.show.active');
        if (!activeTab) {
            console.error('アクティブなタブが見つかりません');
            return;
        }

        // 編集モード内の全ての入力欄を取得
        const editModeDiv = activeTab.querySelector('[id$="-edit-mode"]');
        if (!editModeDiv) {
            console.error('編集モード要素が見つかりません');
            return;
        }

        const editInputs = editModeDiv.querySelectorAll('input[data-original], select[data-original]');

        editInputs.forEach(input => {
            // data-original 属性から元の値を取得
            const originalValue = input.getAttribute('data-original');
            console.log('リセット:', input.name, '元の値:', originalValue);

            if (originalValue !== null) {
                // SELECT要素とINPUT要素で処理を分ける
                if (input.tagName === 'SELECT') {
                    input.value = originalValue;    // セレクトボックスの選択値をリセット
                } else {
                    input.value = originalValue;    // テキスト入力欄の値をリセット
                }
            }
        });

        console.log('入力値リセット完了');
    }
});
</script>

@endsection
