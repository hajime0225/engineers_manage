<div class="mb-3">
    <label for="skills_name" class="form-label">スキル名 <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('skills_name', 'skills_errors') is-invalid @enderror" id="skills_name"
        name="skills_name" value="{{ old('skills_name') }}">
    @error('skills_name', 'skills_errors')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="skills_type" class="form-label">スキル種別 <span class="text-danger">*</span></label>
    <select class="form-select @error('skills_type', 'skills_errors') is-invalid @enderror" id="skills_type" name="skills_type">
        <option value="">選択してください</option>
        @if (isset($skillTypes))
            @foreach ($skillTypes as $value => $label)
                <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        @endif
    </select>
    @error('skills_type', 'skills_errors')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
