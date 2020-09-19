@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="form-row">
                <div class="col-12">
                    <h3 class="mt-1 float-left">{{ isset($team) ? 'Edit team' : 'Create team' }}</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ isset($team) ? route('admin.teams.update', $team->id) : route('admin.teams.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($team)) @method('PATCH') @endif

                <div class="form-group form-row">
                    <label for="name" class="col-sm-2 col-form-label required">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="191" name="name" id="name" placeholder="Name"
                               class="form-control @error('name') is-invalid @enderror col-sm-12"
                               value="{{ old('name', $team->name ?? '' )}}" required>
                        @error('name')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="form-group form-row">
                    <label for="slug" class="col-sm-2 col-form-label required">Slug</label>
                    <div class="col-sm-10 disabled">
                        <input type="slug" maxlength="191" name="slug" id="slug" placeholder="Slug"
                               class="form-control @error('slug') is-invalid @enderror col-sm-10"
                               value="{{ old('slug', $team->slug ?? '' )}}" disabled>
                        <div class="btn btn-lg btn-success btn-block col-sm-2" onclick="event.preventDefault();$('#slug').prop('disabled', function(i, v) { return !v; });let text =$(this).text();$(this).text(text === 'Edit' ? 'Undo' : 'Edit').toggleClass('btn-success').toggleClass('btn-danger');">Edit</div>
                        @error('slug')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="form-group form-row">
                    <label for="url" class="col-sm-2 col-form-label required">URL</label>
                    <div class="col-sm-10">
                        <input type="url" maxlength="191" name="url" id="url" placeholder="URL"
                               class="form-control @error('url') is-invalid @enderror col-sm-12"
                               value="{{ old('url', $team->url ?? '' )}}" required>
                        @error('url')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="form-row text-center">
                    <button type="submit" id="submit" class="btn btn-lg btn-success btn-block">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
