@extends('errors::minimal')

@section('title', __('Insufficient Storage'))
@section('code', '507')
@section('message', $exception->getMessage())
