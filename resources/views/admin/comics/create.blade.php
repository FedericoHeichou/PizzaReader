@extends('admin.comics.form', ['fields' => \App\Models\Comic::getFormFields()])
@section('card-title', 'Add new comic')
@section('form-action', route('admin.comics.store'))
@section('choose-file', 'Choose file')
@foreach(\App\Models\Comic::getFormFields() as $field)
    <?php
        if($field['type'] === 'input_checkbox'){
            $default = isset($field['parameters']['checked']) && $field['parameters']['checked'] ? 1 : 0;
        }else{
            $default = '';
        }
    ?>
    @section($field['parameters']['field'], old($field['parameters']['field'], $default))
@endforeach
