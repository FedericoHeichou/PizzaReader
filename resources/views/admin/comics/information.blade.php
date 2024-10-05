@extends('admin.comics.main')
@section('information')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <h3 class="mt-1 float-sm-start">@yield('card-title')</h3>
                    <a href="@yield('reader_url')" target="_blank" class="btn btn-success ms-sm-3">Read</a>
                    @if(!$is_chapter)
                    <a href="{{ route('admin.comics.stats', $comic->slug) }}" class="btn btn-info text-white ms-1">Stats</a>
                    @else
                    <a href="{{ route('admin.comics.chapters.edit', ['comic' => $comic->slug, 'chapter' => $chapter->id]) }}" class="btn btn-success ms-1">Edit</a>
                    @if(config('settings.cache_proxy_enabled'))
                    <button class="btn btn-warning ms-1" onclick="purgeChapter('@yield('reader_url')')">Purge cache</button>
                    @endif
                    @endif
                    @if(Auth::user()->hasPermission("manager"))
                        @if(!$is_chapter)
                        <a href="{{ route('admin.comics.edit', $comic->slug) }}" class="btn btn-success ms-1">Edit</a>
                        @endif
                        <a href="@yield('form-action')" class="btn btn-danger ms-1"
                            data-bs-toggle="modal" data-bs-target="#modal-container" data-description="@yield('destroy-message')" data-form="destroy-form">Delete</a>
                        <form id="destroy-form" action="@yield('form-action')"
                              method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                    @if($is_chapter)
                    <a href="{{ asset('api' . \App\Models\Chapter::getChapterDownload($comic, $chapter)) }}" class="float-end">
                        <span title="Direct download" class="fa fa-download fa-2x pt-1"></span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @foreach($fields as $field)
                @if($field['type'] !== 'input_hidden')
                    <?php $value = $is_chapter ? $chapter->{$field['parameters']['field']} : $comic->{$field['parameters']['field']} ?>
                    <div class="mb-3 @if(!(($field['type'] === 'input_file' || $field['type'] === 'textarea') && $value))d-flex @endif">
                        <label for="{{ $field['parameters']['field'] }}" class="fw-bold">{{ $field['parameters']['label'] }}:</label>
                        <div class="ms-2">
                            @if($field['type'] === 'input_checkbox') <span>{{ $value ? "Yes" : "No" }}</span>
                            @elseif($field['type'] === 'input_file' && $value) <img src="{{ \App\Models\Comic::getThumbnailUrl($comic) }}" class="img-thumbnail thumbnail">
                            @elseif($field['type'] === 'select') <span>{{ !is_int($value) && $value !== null ? $value : ($value > 0 ? getNameFromId($field['parameters']['options']->toArray(), $value) : 'N/A') }}</span>
                            @elseif($field['type'] === 'textarea') <span class="text-pre">{{ $value ?? 'N/A' }}</span>
                            @elseif($field['type'] === 'input_datetime_local') <span class="convert-timezone">{{ $value ?? 'N/A' }}</span>
                            @else <span>{{ $value ?? 'N/A' }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
