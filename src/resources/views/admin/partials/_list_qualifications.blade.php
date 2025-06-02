@if($items->isNotEmpty())
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>資格名</th>
            <th>登録日</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->created_at->format('Y年m月d日') }}</td>
            <td>
                <button type="button" class="btn btn-sm btn-warning edit-master-button"
                        data-bs-toggle="modal" data-bs-target="#editMasterModal"
                        data-type="qualifications"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}">
                    編集
                </button>
                 <form action="{{ route('admin.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('本当に「{{ $item->name }}」を削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="master_type" value="qualifications">
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <button type="submit" class="btn btn-sm btn-danger">削除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>登録されている資格はありません。</p>
@endif
