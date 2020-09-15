@extends('partials.form.base')
@section('element-' . $field)
        <input {{ isset($required) && $required ? 'required' : '' }} type="hidden" name="{{ $field }}" id="{{ $field }}" value="@yield($field)">
@endsection
