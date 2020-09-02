@extends('admin.comic.form', ['fields' => \App\Chapter::getFormFields()])
@section('card-title', 'Edit comic')
@section('form-action', route('admin.comics.chapters.update', ['comic' => $comic->id, 'chapter' => $chapter->id]))
@section('method', method_field('PATCH'))
@section('choose-file', 'Choose file')
@foreach(\App\Chapter::getFormFields() as $field)
    @section($field['parameters']['field'], old($field['parameters']['field'], $chapter->{$field['parameters']['field']}))
@endforeach
