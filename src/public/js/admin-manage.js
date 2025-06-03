/**
 * マスターデータ管理画面用JavaScript
 * ファイル配置: src/public/js/admin/admin-manage.js
 */

$(document).ready(function() {
    // ========================================
    // 1. DOM要素の取得
    // ========================================
    const $editBtn = $('#edit-btn');           // 編集開始ボタン
    const $cancelBtn = $('#cancel-btn');       // 編集キャンセルボタン
    const $updateBtn = $('#update-btn');       // 更新実行ボタン

    // ========================================
    // 2. 編集モード開始の処理
    // ========================================
    $editBtn.on('click', function() {
        console.log('編集モード開始'); // デバッグ用
        toggleEditMode(true);    // 参照モードから編集モードに切り替え
        toggleButtons(true);     // ボタン表示を編集モード用に切り替える
    });

    // ========================================
    // 3. 編集キャンセルの処理
    // ========================================
    $cancelBtn.on('click', function() {
        console.log('編集キャンセル'); // デバッグ用
        resetFormValues();       // 入力値を元の値に戻す
        toggleEditMode(false);   // 編集モードから参照モードに戻す
        toggleButtons(false);    // ボタン表示を通常モードに戻す
    });

    // ========================================
    // 4. 更新処理の実行
    // ========================================
    $updateBtn.on('click', function() {
        console.log('更新処理開始'); // デバッグ用

        // 現在アクティブなタブ（表示中のタブ）を取得
        const $activeTab = $('.tab-pane.show.active');
        console.log('アクティブタブ:', $activeTab.length > 0 ? $activeTab.attr('id') : 'なし');

        if ($activeTab.length > 0) {
            // アクティブタブ内の編集モード用フォームを取得
            const $editModeDiv = $activeTab.find('[id$="-edit-mode"]');
            if ($editModeDiv.length > 0) {
                const $form = $editModeDiv.find('form');
                console.log('フォーム要素:', $form.length > 0 ? $form.attr('action') : 'なし');

                if ($form.length > 0) {
                    // フォームの内容をサーバーに送信
                    console.log('フォーム送信実行:', $form.attr('action'), $form.attr('method'));
                    $form.submit();
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
    $(document).on('click', '.delete-btn', function(e) {
        console.log('削除ボタンがクリックされました');

        // 確認ダイアログを表示
        if (confirm('本当に削除しますか？')) {
            // ボタンの data-* 属性から削除対象の情報を取得
            const id = $(this).data('id');        // 削除するレコードのID
            const type = $(this).data('type');    // マスター種別（skills/qualifications）

            console.log('削除対象:', { id: id, type: type });

            // 隠しフォームに削除対象の情報をセット
            $('#delete-id').val(id);                    // IDをセット
            $('#delete-master-type').val(type);         // 種別をセット

            // 削除用フォームを送信
            console.log('削除フォーム送信実行');
            $('#delete-form').submit();
        } else {
            console.log('削除がキャンセルされました');
        }
    });

    // ========================================
    // 6. タブ切り替え時の処理（Ajax方式）
    // ========================================
    $('[data-bs-toggle="tab"]').on('click', function() {
        const tabTarget = $(this).attr('data-bs-target');
        console.log('タブ切り替え:', tabTarget);

        // タブ切り替え時は編集モードを強制終了
        resetFormValues();       // 入力値をリセット
        toggleEditMode(false);   // 編集モード終了
        toggleButtons(false);    // ボタン表示を通常モードに戻す

        // タブ名を取得
        const tabName = tabTarget.replace('#', '');

        // Ajaxでタブのデータを取得
        loadTabData(tabName);

        // ブラウザのURLを更新（リロードなし）
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    });

    // ========================================
    // 7. Ajax用のタブデータ読み込み関数
    // ========================================

    /**
     * Ajaxでタブのデータを取得してHTMLを更新
     * @param {string} tabName - 取得するタブ名（'skills' または 'qualifications'）
     */
    function loadTabData(tabName) {
        console.log('Ajaxでタブデータを取得中:', tabName);

        // ローディング表示
        showLoading(tabName);

        // AjaxリクエストをjQueryで送信
        $.ajax({
            url: window.adminTabDataUrl, // Bladeテンプレートから渡されるURL
            method: 'GET',
            data: { tab: tabName },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            dataType: 'json'
        })
        .done(function(data) {
            console.log('Ajaxレスポンス受信:', data);

            if (data.success) {
                // HTMLを更新
                updateTabContent(tabName, data.readModeHtml, data.editModeHtml);
                console.log(`${tabName}タブのデータ更新完了`);
            } else {
                console.error('サーバーエラー:', data.message || 'Unknown error');
                showError(tabName, 'データの取得に失敗しました。');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Ajax通信エラー:', error, status, xhr);
            showError(tabName, '通信エラーが発生しました。');
        })
        .always(function() {
            // ローディング非表示
            hideLoading(tabName);
        });
    }

    /**
     * タブのHTMLコンテンツを更新
     * @param {string} tabName - タブ名
     * @param {string} readModeHtml - 参照モード用HTML
     * @param {string} editModeHtml - 編集モード用HTML
     */
    function updateTabContent(tabName, readModeHtml, editModeHtml) {
        const $readModeDiv = $(`#${tabName}-read-mode`);
        const $editModeDiv = $(`#${tabName}-edit-mode`);

        if ($readModeDiv.length > 0 && $editModeDiv.length > 0) {
            // HTMLを置き換え
            $readModeDiv.html(readModeHtml);

            // 編集モード用のHTMLも更新（フォームタグは保持）
            const $form = $editModeDiv.find('form');
            if ($form.length > 0) {
                // フォーム内のコンテンツのみ更新
                const $tempDiv = $('<div>').html(editModeHtml);
                const newFormContent = $tempDiv.find('form').html();

                $form.html(newFormContent);
            } else {
                // フォームが見つからない場合は全体を更新
                $editModeDiv.html(editModeHtml);
            }

            console.log(`${tabName}のHTMLコンテンツ更新完了`);
        } else {
            console.error(`${tabName}のDOM要素が見つかりません`);
        }
    }

    /**
     * ローディング表示
     * @param {string} tabName - タブ名
     */
    function showLoading(tabName) {
        const $readModeDiv = $(`#${tabName}-read-mode`);
        if ($readModeDiv.length > 0) {
            $readModeDiv.html(`
                <div class="text-center p-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">読み込み中...</span>
                    </div>
                </div>
            `);
        }
    }

    /**
     * ローディング非表示
     * @param {string} tabName - タブ名
     */
    function hideLoading(tabName) {
        // updateTabContent内で実際のコンテンツに置き換えられるため、特別な処理は不要
        console.log(`${tabName}のローディング終了`);
    }

    /**
     * エラー表示
     * @param {string} tabName - タブ名
     * @param {string} message - エラーメッセージ
     */
    function showError(tabName, message) {
        const $readModeDiv = $(`#${tabName}-read-mode`);
        if ($readModeDiv.length > 0) {
            $readModeDiv.html(`<div class="alert alert-danger text-center">${message}</div>`);
        }
    }

    // ========================================
    // 8. ユーティリティ関数群
    // ========================================

    /**
     * 編集モードの表示切り替え（参照⇔編集の切り替え）
     * @param {boolean} isEdit - true: 編集モード, false: 参照モード
     */
    function toggleEditMode(isEdit) {
        console.log('編集モード切り替え:', isEdit ? '編集ON' : '編集OFF');

        // 現在アクティブなタブを取得
        const $activeTab = $('.tab-pane.show.active');
        if ($activeTab.length === 0) {
            console.error('アクティブなタブが見つかりません');
            return;
        }

        // 参照モード用の要素を取得
        const $readModeDiv = $activeTab.find('[id$="-read-mode"]');
        // 編集モード用の要素を取得
        const $editModeDiv = $activeTab.find('[id$="-edit-mode"]');

        console.log('参照モード要素:', $readModeDiv.length);
        console.log('編集モード要素:', $editModeDiv.length);

        if ($readModeDiv.length > 0 && $editModeDiv.length > 0) {
            if (isEdit) {
                // 編集モード: 参照を非表示、編集を表示
                $readModeDiv.hide();
                $editModeDiv.show();
            } else {
                // 参照モード: 参照を表示、編集を非表示
                $readModeDiv.show();
                $editModeDiv.hide();
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
            $editBtn.addClass('d-none');
            $cancelBtn.removeClass('d-none');
            $updateBtn.removeClass('d-none');
        } else {
            // 通常モード: 編集ボタンを表示し、キャンセル・更新ボタンを隠す
            $editBtn.removeClass('d-none');
            $cancelBtn.addClass('d-none');
            $updateBtn.addClass('d-none');
        }
    }

    /**
     * フォームの入力値を元の値に戻す
     */
    function resetFormValues() {
        console.log('入力値をリセット中...');

        // 現在アクティブなタブを取得
        const $activeTab = $('.tab-pane.show.active');
        if ($activeTab.length === 0) {
            console.error('アクティブなタブが見つかりません');
            return;
        }

        // 編集モード内の全ての入力欄を取得
        const $editModeDiv = $activeTab.find('[id$="-edit-mode"]');
        if ($editModeDiv.length === 0) {
            console.error('編集モード要素が見つかりません');
            return;
        }

        const $editInputs = $editModeDiv.find('input[data-original], select[data-original]');

        $editInputs.each(function() {
            const $input = $(this);
            // data-original 属性から元の値を取得
            const originalValue = $input.data('original');
            console.log('リセット:', $input.attr('name'), '元の値:', originalValue);

            if (originalValue !== undefined && originalValue !== null) {
                // SELECT要素とINPUT要素で処理を分ける
                if ($input.is('select')) {
                    $input.val(originalValue);    // セレクトボックスの選択値をリセット
                } else {
                    $input.val(originalValue);    // テキスト入力欄の値をリセット
                }
            }
        });

        console.log('入力値リセット完了');
    }
});
