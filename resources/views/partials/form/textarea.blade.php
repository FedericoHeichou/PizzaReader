<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ isset($required) ? 'required' : '' }}">{{ $label }}</label>
    <div class="col-sm-10">
        <textarea {{ isset($required) ? 'required' : '' }} maxlength="3000"
               class="form-control @error($field) is-invalid @enderror"
               name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}" rows="4"
               value="@yield($field)"></textarea>
        @error($field)
        @include('partials.invalid_feedback')
        @enderror
        <small class="form-text text-muted">
            {{ $hint }}
        </small>
    </div>
</div>
