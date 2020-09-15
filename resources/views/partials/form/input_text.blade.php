@extends('partials.form.base')
@section('element-' . $field)
        <input {{ isset($required) && $required ? 'required' : '' }} type="text" maxlength="{{ $max ?? '191' }}"
               class="form-control @error($field) is-invalid @enderror {{ isset($disabled) ? 'col-sm-10' : 'col-sm-12' }}"
               name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}" pattern="{{ $pattern ?? '.*' }}"
               value="@yield($field)" {{ isset($disabled) ? 'disabled' : '' }}>
@endsection
