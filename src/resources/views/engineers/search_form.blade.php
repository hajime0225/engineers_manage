@extends('layouts.app')

@section('title', 'エンジニア検索')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>エンジニア検索</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('engineers.show') }}" method="GET">
                        {{-- 共通フォーム部分をインクルード --}}
                        @include('engineers.partials._search_form_fields', ['availableSkills' => $availableSkills, 'currentInputs' => $currentInputs])

                        <button type="submit" class="btn btn-primary">検索する</button>
                        {{-- リセットは検索フォーム表示用ルートへ --}}
                        <a href="{{ route('engineers.searchForm') }}" class="btn btn-secondary">リセット</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
