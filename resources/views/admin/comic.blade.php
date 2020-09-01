@extends('admin.series')
@section('information')
    <div class="card">
        <div class="card-header">
            <div class="form-row">
                <div class="col-12">
                    <h3 class="mt-1 float-left">Information about this comic</h3>
                    @if(Auth::user()->hasPermission("manager"))
                        <a href="#TODO" class="btn btn-success ml-3">Read</a>
                        <a href="{{ route('admin.comics.edit', $comic->stub) }}" class="btn btn-success ml-3">Edit</a>
                        <a href="{{ route('admin.comics.destroy', $comic->id) }}" class="btn btn-danger ml-3">Delete</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <todo></todo>
        </div>
    </div>
@endsection
@section('list-title', 'Chapters')
@section('list-buttons')
    <a href="{{ route('admin.comics.chapters.create') }}" class="btn btn-success ml-3">Add chapter</a>
@endsection
@section('search')
    <input id="autocomplete" class="form-control mr-sm-2 ui-autocomplete-input" type="search" placeholder="Search" aria-label="Search" name="text" autocomplete="off">
@endsection
@section('list')
    <div class="list">
        @foreach($chapters as $chapter)
            <div class="item">
                <h5 class="mb-0"><a href="{{ route('admin.comics.chapters.show', $chapter->id) }}">{{ $chapter->title }}</a></h5>
                <span class="small">
                    @if(Auth::user()->hasPermission("manager"))
                        <a href="{{ route('admin.comics.chapters.destroy', $chapter->id) }}">Delete chapter</a>
                        <span class="spacer">|</span>
                    @endif
                    <a href="#">Read</a>
                </span>
            </div>
        @endforeach
    </div>
@endsection
