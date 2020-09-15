@extends('partials.form.form', ['fields' => \App\Models\Comic::getFormFields()])
@section('card-title', 'Edit comic')
@section('form-action', route('admin.comics.update', $comic->id))
@section('method', method_field('PATCH'))
@section('choose-file', 'Choose file')
@foreach(\App\Models\Comic::getFormFields() as $field)
    @section($field['parameters']['field'], old($field['parameters']['field'], isset($comic->{$field['parameters']['field']}) ? $comic->{$field['parameters']['field']} : ($field['parameters']['default'] ?? '') ))
@endforeach
