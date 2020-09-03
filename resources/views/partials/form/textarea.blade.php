@extends('partials.form.base')
@section('element-' . $field)
        <textarea {{ isset($required) ? 'required' : '' }} maxlength="3000"
               class="form-control @error($field) is-invalid @enderror"
               name="{{ $field }}" id="{{ $field }}" placeholder="{{ $label }}" rows="4"
               value="@yield($field)"></textarea>
@endsection
