<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ $required ?? '' }}">{{ $label }}</label>
    <div class="col-sm-10">
        <div class="custom-file">
            <input {{ $required ?? '' }} type="file" accept=".jpg, .jpeg, .png, .gif, .webp"
                   class="form-control custom-file-input @error($field) is-invalid @enderror"
                   name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}"
                   value="@yield($field)">
            <label class="custom-file-label" for="{{ $field }}">Choose file</label>
        </div>
        @error($field)
        @include('partials.invalid_feedback')
        @enderror
        <small class="form-text text-muted">
            {{ $hint }}
        </small>
    </div>
</div>
