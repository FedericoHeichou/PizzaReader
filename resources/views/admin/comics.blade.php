@extends('admin.series')
@section('list-title', 'Your Comics')
@section('list-buttons')
    <a href="{{ route('admin.comics.create') }}" class="btn btn-success ml-3">Add comic</a>
@endsection
@section('search')
    <input id="autocomplete" class="form-control mr-sm-2 ui-autocomplete-input" type="search" placeholder="Search" aria-label="Search" name="text" autocomplete="off">
@endsection
@section('list')
    <div class="list">
        @foreach($comics as $comic)
            <div class="item">
                <h5 class="mb-0"><a href="{{ route('admin.comics.show', $comic->stub) }}">{{ $comic->name }}</a></h5>
                <span class="small">
                    <a href="{{ route('admin.comics.chapters.create') }}">Add chapter</a>
                    @if(Auth::user()->hasPermission("manager"))
                        <span class="spacer">|</span><a href="{{ route('admin.comics.destroy', $comic->id) }}">Delete comic</a>
                    @endif
                    <span class="spacer">|</span><a href="#">Read</a>
                </span>
            </div>
        @endforeach
    </div>
@endsection
