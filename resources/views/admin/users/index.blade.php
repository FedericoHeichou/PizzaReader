@extends('layouts.admin')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <div class="form-row">
                <div class="col-sm-9 col-6">
                    <h3 class="mt-1 float-left">Users</h3>
                </div>
                <div class="col-sm-3 col-6">
                    @include('partials.card-search')
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row border-bottom py-1 d-none d-md-flex">
                <div class="col-md-3 text-left">Name</div>
                <div class="col-md-3 text-left">Email</div>
                <div class="col-md-2 text-left">Last login</div>
                <div class="col text-left pr-0">Change role</div>
                <div class="col-auto text-right pl-1">Options</div>
            </div>
            @foreach($users as $user)
                <div class="row flex-md-nowrap text-truncate border-bottom py-1 item users">
                    <div class="col-md-3 text-left"><span class="filter">{{ $user->name }}</span></div>
                    <div class="col-md-3 text-left">{{ $user->email }}</div>
                    <div class="col-md-2 text-left">{{ $user->last_login ? Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $user->last_login, 'UTC')->tz(Auth::user()->timezone) : 'N/A' }}</div>
                    <div class="col text-center pr-0">
                        <form id="edit-{{ $user->id }}" action="{{ route('admin.users.update', $user->id) }}"
                              method="POST" class="d-none">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" class="role" name="role" value="{{ $user->role_id }}">
                        </form>
                        <select class="role custom-select"  data-user="{{ $user->id }}" @if(!Auth::user()->hasPermission('admin') || Auth::user()->id === $user->id || $user->id === 1) disabled @endif>
                        @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $role->id === $user->role_id ? ' selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-auto text-right pl-1">
                        @if(Auth::user()->hasPermission('manager') && !$user->hasPermission('manager'))
                            <button href="#" class="btn btn-primary" onclick="assignComicsBox({'id': '{{ $user->id }}', 'name': '{{ $user->name }}'}, {{ $user->comicsMinimal }})">Assign</button>
                        @else
                            <a href="#" class="btn btn-secondary" title="You can't assign this user" onclick="event.preventDefault()">Assign</a>
                        @endif
                        @if((Auth::user()->hasPermission('admin') || Auth::user()->id === $user->id) && ($user->id !== 1 || Auth::user()->id === 1))
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-success">Edit</a>
                        @else
                            <a href="#" class="btn btn-secondary" title="You can't edit this user" onclick="event.preventDefault()">Edit</a>
                        @endif
                        @if(!Auth::user()->hasPermission('admin') || Auth::user()->id === $user->id || $user->id === 1)
                            <a href="#" class="btn btn-secondary" title="You can't delete this user" onclick="event.preventDefault()">Delete</a>
                        @else
                            <form id="delete-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <a href="{{ route('admin.users.destroy', $user->id) }}" class="btn btn-danger"
                               onclick="confirmbox('Do you want to delete {{ $user->name }}?', 'delete-{{ $user->id }}')">Delete</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        {{ $users->links() }}
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
