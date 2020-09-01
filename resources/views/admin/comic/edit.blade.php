@extends('admin.comic.form')
@section('title', 'Edit comic')
@section('action', route('admin.comics.update', $comic->id))
@section('method', @method('PATCH'))
