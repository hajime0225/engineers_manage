<div class="mb-3">
    <label for="qualifications_name" class="form-label">資格名 <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('qualifications_name', 'qualifications_errors') is-invalid @enderror" id="qualifications_name" name="qualifications_name" value="{{ old('name') }}">
    @error('qualifications_name', 'qualifications_errors')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
