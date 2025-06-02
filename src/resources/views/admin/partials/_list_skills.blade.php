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
        @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $item->type)) }}</td>
            <td>{{ $item->created_at->format('Y年m月d日') }}</td>
            <td>
                <button type="button" class="btn btn-sm btn-warning edit-master-button"
                        data-bs-toggle="modal" data-bs-target="#editMasterModal"
                        data-type="skills"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-skill-type="{{ $item->type }}">
                    編集
                </button>
                <form action="{{ route('admin.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('本当に「{{ $item->name }}」を削除しますか？');">
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
