@extends('partials.form.base')
@section('element-' . $field)
        <input type="hidden" name="{{ $field }}" id="{{ $field }}" value="@yield($field)">
@endsection
