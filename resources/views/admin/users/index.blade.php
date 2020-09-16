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
                        @if((Auth::user()->hasPermission('admin') || Auth::user()->id === $user->id) && ($user->id !== 1 || Auth::user()->id === 1))
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-success">Edit</a>
                        @else
                            <a href="#" class="btn btn-secondary" title="You can't edit this user" onclick="event.preventDefault()">Edit</a>
                        @endif
                        @if(!Auth::user()->hasPermission('admin') || Auth::user()->id === $user->id || $user->id === 1)
                            <a href="#" class="btn btn-secondary" title="You can't delete this user" onclick="event.preventDefault()">
                        @else
                        <form id="delete-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}"
                              method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        <a href="{{ route('admin.users.destroy', $user->id) }}" class="btn btn-danger"
                           onclick="confirmbox('Do you want to delete {{ $user->name }}?', 'delete-{{ $user->id }}')">
                        @endif
                            Delete
                        </a>

                    </div>
                </div>
            @endforeach
        </div>
        {{ $users->links() }}
    </div>
@endsection
