@extends('partials.form.base')
@section('element-' . $field)
        <input {{ isset($required) ? 'required' : '' }} type="datetime-local" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}"
               class="form-control convert-timezone @error($field) is-invalid @enderror {{ isset($disabled) ? 'col-sm-10' : 'col-sm-12' }}"
               name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}"
               value="{{ $__env->yieldContent($field) ? date('Y-m-d\TH:i', strtotime($__env->yieldContent($field))) : '' }}"
            {{ isset($disabled) ? 'disabled' : '' }}>
@endsection
