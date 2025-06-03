@if($items->count() > 0)
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
                    {{-- ID列（編集不可） --}}
                    <td>
                        {{ $item->id }}
                        <input type="hidden" name="records[{{ $index }}][id]" value="{{ $item->id }}">
                    </td>

                    {{-- 資格名列（編集可能） --}}
                    <td>
                        <input type="text" class="form-control"
                               name="records[{{ $index }}][name]"
                               value="{{ old('records.'.$index.'.name', $item->name) }}"
                               data-original="{{ $item->name }}"
                               required>
                    </td>

                    {{-- 作成日・更新日（編集不可） --}}
                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->updated_at->format('Y-m-d H:i') }}</td>

                    {{-- 削除ボタン --}}
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                data-id="{{ $item->id }}"
                                data-type="qualifications">
                            削除
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-center mt-3">資格データがありません。</p>
@endif
