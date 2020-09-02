@extends('admin.comic.form', ['fields' => \App\Comic::getFormFields()])
@section('card-title', 'Edit comic')
@section('form-action', route('admin.comics.update', $comic->id))
@section('method', method_field('PATCH'))
@section('choose-file', 'Choose file')
@foreach(\App\Comic::getFormFields() as $field)
    @section($field['parameters']['field'], old($field['parameters']['field'], $comic->{$field['parameters']['field']}))
@endforeach
