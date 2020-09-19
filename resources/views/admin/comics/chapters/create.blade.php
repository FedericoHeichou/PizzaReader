@extends('partials.form.form', ['fields' => \App\Models\Chapter::getFormFields()])
@section('card-title', 'Add new chapter')
@section('form-action', route('admin.comics.chapters.store', $comic->slug))
@section('choose-file', 'Choose file')
@foreach(\App\Models\Chapter::getFormFields() as $field)
    <?php
        if($field['type'] === 'input_checkbox'){
            $default = isset($field['parameters']['checked']) && $field['parameters']['checked'] ? 1 : 0;
        }else{
            $default = '';
        }
    ?>
    @section($field['parameters']['field'], old($field['parameters']['field'], $default))
@endforeach
