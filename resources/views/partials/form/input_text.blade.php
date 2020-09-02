<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ isset($required) ? 'required' : '' }}">{{ $label }}</label>
    <div class="col-sm-10">
        <input {{ isset($required) ? 'required' : '' }} type="text" maxlength="191"
               class="form-control @error($field) is-invalid @enderror"
               name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}"
               value="@yield($field)">
        @error($field)
        @include('partials.invalid_feedback')
        @enderror
        <small class="form-text text-muted">
            {{ $hint }}
        </small>
    </div>
</div>
