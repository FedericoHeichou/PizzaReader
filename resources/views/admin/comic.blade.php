@extends('admin.comic.show', ['fields' => \App\Comic::getFormFields(), 'is_chapter' => false])
@section('card-title', 'Information about this comic')
@section('form-action', route('admin.comics.destroy', $comic->id))
@section('list-title', 'Chapters')
@section('list-buttons')
    <a href="{{ route('admin.comics.chapters.create', $comic->slug) }}" class="btn btn-success ml-3">Add chapter</a>
@endsection
@section('list')
    <div class="list">
        @foreach($chapters as $chapter)
            <div class="item">
                <h5 class="mb-0"><a href="{{ route('admin.comics.chapters.show', ['comic' => $comic->slug, 'chapter' => $chapter->id]) }}">{{ $chapter->title }}</a></h5>
                <span class="small">
                    @if(Auth::user()->hasPermission("manager"))
                        <a href="{{ route('admin.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                           onclick="event.preventDefault(); document.getElementById('destroy-chapter-form-{{ $chapter->id }}').submit();">
                            Delete chapter</a>
                        <form id="destroy-chapter-form-{{ $chapter->id }}" action="{{ route('admin.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                              method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        <span class="spacer">|</span>
                    @endif
                    <a href="#">Read</a>
                </span>
            </div>
        @endforeach
    </div>
@endsection
