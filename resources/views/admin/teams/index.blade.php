@extends('layouts.admin')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <div class="form-row">
                <div class="col-sm-9 col-6">
                    <h3 class="mt-1 float-left">Teams</h3>
                    <a href="{{ route('admin.teams.create') }}" class="btn btn-success ml-3">Add team</a>
                </div>
                <div class="col-sm-3 col-6">
                    @include('partials.card-search')
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row border-bottom py-1 d-none d-md-flex">
                <div class="col-md-3 text-left">Name</div>
                <div class="col-md-3 text-left">Slug</div>
                <div class="col-md-4 text-left">URL</div>
                <div class="col-auto text-right pl-1">Options</div>
            </div>
            @foreach($teams as $team)
                <div class="row flex-md-nowrap text-truncate border-bottom py-1 item teams">
                    <div class="col-md-3 text-left"><span class="filter">{{ $team->name }}</span></div>
                    <div class="col-md-3 text-left">{{ $team->slug }}</div>
                    <div class="col-md-4 text-left"><a href="{{ $team->url }}" target="_blank">{{ $team->url }}</a></div>
                    <div class="col-auto text-right pl-0">
                        @if(Auth::user()->hasPermission('admin'))
                            <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-success">Edit</a>
                            <form id="delete-{{ $team->id }}" action="{{ route('admin.teams.destroy', $team->id) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <a href="{{ route('admin.teams.destroy', $team->id) }}" class="btn btn-danger"
                               onclick="confirmbox('Do you want to delete {{ $team->name }}? Their chapter will be not deleted but they can\'t be edited until you select a new team', 'delete-{{ $team->id }}')">Delete</a>
                        @else
                            <a href="#" class="btn btn-secondary" title="You can't edit this team" onclick="event.preventDefault()">Edit</a>
                            <a href="#" class="btn btn-secondary" title="You can't delete this team" onclick="event.preventDefault()">Delete</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        {{ $teams->links() }}
    </div>

    <div id="modal-assign" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Assign comics</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <input id="comic-search" type="search" placeholder="Search comic" aria-label="Search comic"
                           name="search" class="form-control mr-sm-2" autocomplete="off">
                    <div id="results-box" style="display: none"></div>
                </div>
                <div id="assigned-comics" class="modal-body">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="assignComics()">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection
