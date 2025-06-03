@extends('layouts.admin.app')

@section('title', '管理者用 - マスターデータ登録')

@push('styles')
<style>
    .tab-content .tab-pane {
        display: none;
    }
    .tab-content .tab-pane.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2>マスターデータ登録</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>入力エラーがあります:</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- タブナビゲーション --}}
            <ul class="nav nav-tabs mb-3" id="masterDataTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="skills-tab-button" data-bs-toggle="tab" data-bs-target="#skills-form-content" type="button" role="tab" aria-controls="skills-form-content" aria-selected="false" data-master-type="skills">スキル</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="qualifications-tab-button" data-bs-toggle="tab" data-bs-target="#qualifications-form-content" type="button" role="tab" aria-controls="qualifications-form-content" aria-selected="false" data-master-type="qualifications">資格</button>
                </li>
            </ul>

            <form action="{{ route('admin.store') }}" method="POST" id="masterDataForm">
                @csrf
                <input type="hidden" name="master_type" id="master_type_input" value="">

                {{-- タブコンテンツ表示エリア --}}
                <div class="tab-content" id="masterDataTabContent">
                    <div class="tab-pane fade" id="skills-form-content" role="tabpanel" aria-labelledby="skills-tab-button">
                        @include('admin.partials._create_skills_form', ['skillTypes' => $skillTypes])
                    </div>
                    <div class="tab-pane fade" id="qualifications-form-content" role="tabpanel" aria-labelledby="qualifications-tab-button">
                        @include('admin.partials._create_qualifications_form')
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="submitButton" disabled>登録する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const masterDataTabs = document.getElementById('masterDataTabs');
    const masterTypeInput = document.getElementById('master_type_input');
    const submitButton = document.getElementById('submitButton');
    const tabPanes = document.querySelectorAll('.tab-content .tab-pane');
    let firstTabButton = null;

    const tabButtons = masterDataTabs.querySelectorAll('[data-bs-toggle="tab"]');

    tabButtons.forEach((button, index) => {
        if (index === 0) {
            firstTabButton = button;
        }
        const tabPaneId = button.getAttribute('data-bs-target');
        const tabPane = document.querySelector(tabPaneId);

        button.addEventListener('show.bs.tab', function (event) {
            masterTypeInput.value = event.target.dataset.masterType;
            submitButton.disabled = false;

            tabPanes.forEach(pane => {
                if (pane.id === tabPaneId.substring(1)) {
                    pane.classList.add('active', 'show');
                } else {
                    pane.classList.remove('active', 'show');
                }
            });
        });

    });

    document.getElementById('masterDataForm').addEventListener('submit', function(event) {
        if (!masterTypeInput.value) {
            alert('登録するマスターデータの種類を選択してください。');
            event.preventDefault();
        }
    });
});
</script>
@endpush
