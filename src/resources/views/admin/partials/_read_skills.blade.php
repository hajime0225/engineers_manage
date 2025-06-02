@if($items->count() > 0)
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
                @foreach($items as $item)
                <tr data-id="{{ $item->id }}">
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $skillTypes[$item->type] ?? $item->type }}</td>
                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                data-id="{{ $item->id }}"
                                data-type="skills">
                            削除
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-center mt-3">スキルデータがありません。</p>
@endif
