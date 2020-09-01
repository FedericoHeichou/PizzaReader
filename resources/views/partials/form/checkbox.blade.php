<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ $required ?? '' }}">{{ $label }}</label>
    <div class="ml-2">
        <div class="form-check comic-check">
            <input {{ $required ?? '' }} type="checkbox"
                   class="form-check-input @error($field) is-invalid @enderror"
                   name="{{ $field }}" id="{{ $field }}" {{ $checked ?? '' }}>
            <label for="{{ $field }}" class="form-check-label {{ $required ?? '' }}">Enable</label>
        </div>
        @error($field)
        @include('partials.invalid_feedback')
        @enderror
        <small class="form-text text-muted">
            {{ $hint }}
        </small>
    </div>
</div>
