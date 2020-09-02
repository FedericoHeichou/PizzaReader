<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ isset($required) ? 'required' : '' }}">{{ $label }}</label>
    <div class="ml-2">
        <div class="form-check comic-check">
            <input type="hidden" name="{{ $field }}" value="0" />
            <input {{ isset($required) ? 'required' : '' }} type="checkbox"
                   class="form-check-input @error($field) is-invalid @enderror" value="@yield($field, 0)"
                   name="{{ $field }}" id="{{ $field }}" {{ $__env->yieldContent($field) ? 'checked' : '' }} >
            <label for="{{ $field }}" class="form-check-label {{ isset($required) ? 'required' : '' }}">Enable</label>
        </div>
        @error($field)
        @include('partials.invalid_feedback')
        @enderror
        <small class="form-text text-muted">
            {{ $hint }}
        </small>
    </div>
</div>
