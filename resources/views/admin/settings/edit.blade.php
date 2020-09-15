@extends('partials.form.form', ['fields' => \App\Models\Settings::getFormFields()])
@section('card-title', 'Edit settings')
@section('form-action', route('admin.settings.update'))
@section('method', method_field('PATCH'))
@section('choose-file', 'Choose file')
@foreach(\App\Models\Settings::getFormFields() as $field)
    @section($field['parameters']['field'], old($field['parameters']['field'], isset($settings[$field['parameters']['field']]) ? $settings[$field['parameters']['field']] : ($field['parameters']['default'] ?? '') ))
@endforeach
