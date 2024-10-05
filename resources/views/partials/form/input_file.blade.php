@extends('partials.form.base')
@section('element-' . $field)
        <input {{ isset($required) && $required ? 'required' : '' }} type="file"
                accept="{{ isset($accept) && $accept ? $accept : '.jpg, .jpeg, .png, .gif, .webp' }}"
                class="form-control @error($field) is-invalid @enderror {{ (isset($disabled) && $disabled) || (isset($clear) && $clear) ? 'col-sm-8' : 'col-sm-10' }}"
                name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}"
                value="@yield($field)" {{ isset($disabled) && $disabled ? 'disabled' : '' }}>
@endsection
