@if($items->isNotEmpty())
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>スキル名</th>
            <th>種別</th>
            <th>登録日</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $index => $item)
        <tr>
            <td>{{ $item->id }} <input type="hidden" name="records[{{ $index }}][id]" value="{{ $item->id }}"></td>
            <td class="editable-cell">
                <span class="view-text">{{ $item->name }}</span>
                <input type="text" class="form-control" name="records[{{ $index }}][name]" value="{{ $item->name }}">
            </td>
            <td class="editable-cell">
                <span class="view-text">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</span>
                <select class="form-select" name="records[{{ $index }}][type]">
                    @foreach($skillTypes as $value => $label)
                        <option value="{{ $value }}" {{ $item->type == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            <td>{{ $item->created_at->format('Y年m月d日') }}</td> {{-- 登録日は編集不可 --}}
            <td>
                <form action="{{ route('admin.destroy') }}" method="POST" class="d-inline delete-form" data-confirm-message="本当に「{{ $item->name }}」を削除しますか？">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="master_type" value="skills">
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <button type="submit" class="btn btn-sm btn-danger">削除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>登録されているスキルはありません。</p>
@endif
