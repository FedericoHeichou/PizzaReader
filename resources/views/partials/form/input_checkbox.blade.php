@extends('partials.form.base')
@section('element-' . $field)
        <div class="form-check comic-check">
            <input type="hidden" name="{{ $field }}" value="0" />
            <input type="checkbox"
                   class="form-check-input @error($field) is-invalid @enderror" value="@yield($field, 0)"
                   name="{{ $field }}" id="{{ $field }}" {{ $__env->yieldContent($field) ? 'checked' : '' }} >
            <label for="{{ $field }}" class="form-label form-check-label">Enable</label>
        </div>
@endsection
