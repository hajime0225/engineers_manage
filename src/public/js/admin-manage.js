/**
 * マスターデータ管理画面用JavaScript
 * ファイル配置: src/public/js/admin/admin-manage.js
 */

$(document).ready(function() {
    // ========================================
    // 1. DOM要素の取得とデータキャッシュ
    // ========================================
    const $editBtn = $('#edit-btn');
    const $cancelBtn = $('#cancel-btn');
    const $updateBtn = $('#update-btn');

    // データキャッシュ用オブジェクト
    let tabDataCache = {
        skills: null,
        qualifications: null
    };

    // ========================================
    // 2. 編集モード開始の処理
    // ========================================
    $editBtn.on('click', function() {
        console.log('編集モード開始');

        const $activeTab = $('.tab-pane.show.active');
        const tabName = $activeTab.attr('id');

        if (tabName && tabDataCache[tabName]) {
            // キャッシュデータで編集モードを初期化
            updateEditModeContent(tabName, tabDataCache[tabName].readModeHtml);
        }

        toggleEditMode(true);
        toggleButtons(true);
    });

    // ========================================
    // 3. 編集キャンセルの処理
    // ========================================
    $cancelBtn.on('click', function() {
        console.log('編集キャンセル');
        resetFormValues();
        toggleEditMode(false);
        toggleButtons(false);
    });

    // ========================================
    // 4. 更新処理の実行
    // ========================================
    $updateBtn.on('click', function() {
        console.log('更新処理開始');

        const $activeTab = $('.tab-pane.show.active');
        console.log('アクティブタブ:', $activeTab.length > 0 ? $activeTab.attr('id') : 'なし');

        if ($activeTab.length > 0) {
            const $editModeDiv = $activeTab.find('[id$="-edit-mode"]');
            if ($editModeDiv.length > 0) {
                const $form = $editModeDiv.find('form');

                if ($form.length > 0) {
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

        if (confirm('本当に削除しますか？')) {
            const id = $(this).data('id');
            const type = $(this).data('type');

            console.log('削除対象:', { id: id, type: type });

            $('#delete-id').val(id);
            $('#delete-master-type').val(type);

            console.log('削除フォーム送信実行');
            $('#delete-form').submit();
        } else {
            console.log('削除がキャンセルされました');
        }
    });

    // ========================================
    // 6. タブ切り替え時の処理
    // ========================================
    $('[data-bs-toggle="tab"]').on('click', function() {
        const tabTarget = $(this).attr('data-bs-target');
        console.log('タブ切り替え:', tabTarget);

        // タブ切り替え時は編集モードを強制終了
        console.log('タブ切り替え: 編集モード終了処理開始');
        resetFormValues();
        toggleEditMode(false);
        toggleButtons(false);
        console.log('タブ切り替え: 編集モード終了処理完了');

        const tabName = tabTarget.replace('#', '');

        // Ajaxでタブのデータを取得
        loadTabData(tabName);

        // ブラウザのURLを更新
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    });

    // タブ切り替え完了後の処理（Bootstrap のタブイベントを使用）
    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const tabTarget = $(e.target).attr('data-bs-target');
        const tabName = tabTarget.replace('#', '');
        console.log('タブ切り替え完了:', tabName);

        // 確実に参照モードに戻す
        console.log('タブ切り替え完了: 参照モード強制切り替え開始');
        const $newActiveTab = $(tabTarget);
        const $readModeDiv = $newActiveTab.find('[id$="-read-mode"]');
        const $editModeDiv = $newActiveTab.find('[id$="-edit-mode"]');

        if ($readModeDiv.length > 0 && $editModeDiv.length > 0) {
            $readModeDiv.show();
            $editModeDiv.hide();
            console.log('タブ切り替え完了: 参照モード強制切り替え完了');
        }

        // ボタン状態も確実にリセット
        $editBtn.removeClass('d-none');
        $cancelBtn.addClass('d-none');
        $updateBtn.addClass('d-none');
        console.log('タブ切り替え完了: ボタン状態リセット完了');
    });

    // ========================================
    // 7. Ajax用のタブデータ読み込み関数
    // ========================================

    /**
     * Ajaxでタブのデータを取得してキャッシュ・表示更新
     */
    function loadTabData(tabName) {
        console.log('Ajaxでタブデータを取得中:', tabName);

        showLoading(tabName);

        $.ajax({
            url: window.adminTabDataUrl,
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
                // データをキャッシュに保存
                tabDataCache[tabName] = data;
                console.log(`${tabName}データをキャッシュに保存:`, data);

                // 参照モードのHTMLを更新
                updateReadModeContent(tabName, data.readModeHtml);

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
            hideLoading(tabName);
        });
    }

    /**
     * 参照モードのHTMLコンテンツを更新
     */
    function updateReadModeContent(tabName, readModeHtml) {
        const $readModeDiv = $(`#${tabName}-read-mode`);

        if ($readModeDiv.length > 0) {
            $readModeDiv.html(readModeHtml);
            console.log(`${tabName}の参照モードHTMLコンテンツ更新完了`);
        } else {
            console.error(`${tabName}の参照モード要素が見つかりません`);
        }
    }

    /**
     * 編集モードのHTMLコンテンツを更新
     */
    function updateEditModeContent(tabName, readModeHtml) {
        console.log(`${tabName}の編集モード更新開始`);

        const $editModeDiv = $(`#${tabName}-edit-mode`);

        if ($editModeDiv.length > 0) {
            const $form = $editModeDiv.find('form');

            if ($form.length > 0) {
                // 編集モード用HTMLを生成
                const editModeHtml = convertToEditMode(readModeHtml, tabName);

                // フォーム内の既存コンテンツを更新（hiddenフィールドは保持）
                const $hiddenInputs = $form.find('input[type="hidden"]');
                $form.empty();
                $hiddenInputs.each(function() {
                    $form.append($(this));
                });
                $form.append(editModeHtml);

                console.log(`${tabName}の編集モードHTMLコンテンツ更新完了`);
            } else {
                console.error(`${tabName}の編集モードフォームが見つかりません`);
            }
        } else {
            console.error(`${tabName}の編集モード要素が見つかりません`);
        }
    }

    /**
     * 参照モード用HTMLを編集モード用HTMLに変換
     */
    function convertToEditMode(readModeHtml, tabName) {
        console.log(`${tabName}の編集モード変換開始`);

        const $tempDiv = $('<div>').html(readModeHtml);
        const $table = $tempDiv.find('table tbody');

        if ($table.length === 0) {
            return '<p class="text-center mt-3">データがありません。</p>';
        }

        let editHtml = '<div class="table-responsive"><table class="table table-striped table-hover"><thead>';

        if (tabName === 'skills') {
            editHtml += '<tr><th>ID</th><th>スキル名</th><th>スキル種別</th><th>作成日</th><th>更新日</th><th>操作</th></tr>';
        } else {
            editHtml += '<tr><th>ID</th><th>資格名</th><th>作成日</th><th>更新日</th><th>操作</th></tr>';
        }

        editHtml += '</thead><tbody>';

        $table.find('tr').each(function(index) {
            const $row = $(this);
            const id = $row.find('td:first').text().trim();
            const name = $row.find('td:nth-child(2)').text().trim();
            const created = $row.find('td:nth-last-child(2)').text().trim();
            const updated = $row.find('td:last').prev().text().trim();

            editHtml += `<tr data-id="${id}">`;
            editHtml += `<td>${id}<input type="hidden" name="records[${index}][id]" value="${id}"></td>`;
            editHtml += `<td><input type="text" class="form-control" name="records[${index}][name]" value="${name}" data-original="${name}" required></td>`;

            if (tabName === 'skills') {
                // スキルの場合は種別セレクトボックス
                const typeText = $row.find('td:nth-child(3)').text().trim();
                const typeValue = getSkillTypeValue(typeText);

                editHtml += `<td><select class="form-control" name="records[${index}][type]" data-original="${typeValue}" required>`;
                editHtml += '<option value="programming_language"' + (typeValue === 'programming_language' ? ' selected' : '') + '>プログラミング言語</option>';
                editHtml += '<option value="framework"' + (typeValue === 'framework' ? ' selected' : '') + '>フレームワーク/ライブラリ</option>';
                editHtml += '<option value="database"' + (typeValue === 'database' ? ' selected' : '') + '>データベース</option>';
                editHtml += '<option value="os_cloud"' + (typeValue === 'os_cloud' ? ' selected' : '') + '>OS/クラウド環境</option>';
                editHtml += '<option value="tool"' + (typeValue === 'tool' ? ' selected' : '') + '>ツール</option>';
                editHtml += '<option value="other"' + (typeValue === 'other' ? ' selected' : '') + '>その他</option>';
                editHtml += '</select></td>';
            }

            editHtml += `<td>${created}</td>`;
            editHtml += `<td>${updated}</td>`;
            editHtml += `<td><button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${id}" data-type="${tabName}">削除</button></td>`;
            editHtml += '</tr>';
        });

        editHtml += '</tbody></table></div>';

        return editHtml;
    }

    /**
     * スキル種別の表示名から値を取得
     */
    function getSkillTypeValue(displayText) {
        const mapping = {
            'プログラミング言語': 'programming_language',
            'フレームワーク/ライブラリ': 'framework',
            'データベース': 'database',
            'OS/クラウド環境': 'os_cloud',
            'ツール': 'tool',
            'その他': 'other'
        };
        return mapping[displayText] || 'other';
    }

    /**
     * ローディング表示
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
     */
    function hideLoading(tabName) {
        console.log(`${tabName}のローディング終了`);
    }

    /**
     * エラー表示
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
     * 編集モードの表示切り替え
     */
    function toggleEditMode(isEdit) {
        console.log('編集モード切り替え:', isEdit ? '編集ON' : '編集OFF');

        const $activeTab = $('.tab-pane.show.active');
        if ($activeTab.length === 0) {
            console.error('アクティブなタブが見つかりません');
            return;
        }

        const $readModeDiv = $activeTab.find('[id$="-read-mode"]');
        const $editModeDiv = $activeTab.find('[id$="-edit-mode"]');

        if ($readModeDiv.length > 0 && $editModeDiv.length > 0) {
            if (isEdit) {
                $readModeDiv.hide();
                $editModeDiv.show();
            } else {
                $readModeDiv.show();
                $editModeDiv.hide();
            }
        } else {
            console.error('参照モードまたは編集モードの要素が見つかりません');
        }
    }

    /**
     * ボタン表示の切り替え
     */
    function toggleButtons(isEdit) {
        console.log('ボタン表示切り替え:', isEdit ? '編集中' : '通常');

        if (isEdit) {
            $editBtn.addClass('d-none');
            $cancelBtn.removeClass('d-none');
            $updateBtn.removeClass('d-none');
        } else {
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

        const $activeTab = $('.tab-pane.show.active');
        if ($activeTab.length === 0) {
            console.error('アクティブなタブが見つかりません');
            return;
        }

        const $editModeDiv = $activeTab.find('[id$="-edit-mode"]');
        if ($editModeDiv.length === 0) {
            console.error('編集モード要素が見つかりません');
            return;
        }

        const $editInputs = $editModeDiv.find('input[data-original], select[data-original]');

        $editInputs.each(function() {
            const $input = $(this);
            const originalValue = $input.data('original');
            console.log('リセット:', $input.attr('name'), '元の値:', originalValue);

            if (originalValue !== undefined && originalValue !== null) {
                if ($input.is('select')) {
                    $input.val(originalValue);
                } else {
                    $input.val(originalValue);
                }
            }
        });

        console.log('入力値リセット完了');
    }
});
