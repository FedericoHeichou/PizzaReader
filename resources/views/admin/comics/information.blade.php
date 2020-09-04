@extends('admin.comics.main')
@section('information')
    <div class="card mb-4">
        <div class="card-header">
            <div class="form-row">
                <div class="col-12">
                    <h3 class="mt-1 float-left">@yield('card-title')</h3>
                    <a href="#TODO" class="btn btn-success ml-3">Read</a>
                    @if($is_chapter)
                    <a href="{{ route('admin.comics.chapters.edit', ['comic' => $comic->slug, 'chapter' => $chapter->id]) }}" class="btn btn-success ml-1">Edit</a>
                    @endif
                    @if(Auth::user()->hasPermission("manager"))
                        @if(!$is_chapter)
                        <a href="{{ route('admin.comics.edit', $comic->slug) }}" class="btn btn-success ml-1">Edit</a>
                        @endif
                        <a href="@yield('form-action')" class="btn btn-danger ml-1"
                           onclick="confirmbox('@yield("destroy-message")', 'destroy-form')">
                            Delete</a>
                        <form id="destroy-form" action="@yield('form-action')"
                              method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @foreach($fields as $field)
                <?php $value = $is_chapter ? $chapter->{$field['parameters']['field']} : $comic->{$field['parameters']['field']} ?>
                <div class="form-group @if(!($field['type'] === 'input_file' && $value))form-row @endif">
                    <label for="{{ $field['parameters']['field'] }}" class="font-weight-bold">{{ $field['parameters']['label'] }}:</label>
                    <div class="ml-2">
                        @if($field['type'] === 'input_checkbox') {{ $value ? "Yes" : "No" }}
                        @elseif($field['type'] === 'input_file' && $value) <img src="{{ \App\Comic::getThumbnailUrl($comic) }}" class="img-thumbnail thumbnail">
                        @elseif($field['type'] === 'select') {{ !is_int($value) && $value !== null ? $value : ($value > 0 ? $field['parameters']['options'][$value - 1]->name : 'N/A') }}
                        @elseif($field['type'] === 'textarea') {!! nl2br(e($value ?? 'N/A')) !!}
                        @else <span>{{ $value ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
