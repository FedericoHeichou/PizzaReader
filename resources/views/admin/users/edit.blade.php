@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <h3 class="mt-1 float-start">Edit profile</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-3 row">
                    <label for="name" class="form-label col-sm-2 col-form-label required">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="191" name="name" id="name" placeholder="Name"
                               class="form-control @error('name') is-invalid @enderror col-sm-12"
                               value="{{ old('name', $user->name )}}" required>
                        @error('name')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="email" class="form-label col-sm-2 col-form-label required">Email</label>
                    <div class="col-sm-10">
                        <input type="email" maxlength="191" name="email" id="email" placeholder="Email"
                               class="form-control @error('email') is-invalid @enderror col-sm-12"
                               value="{{ old('email', $user->email )}}" required>
                        @error('email')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->id === $user->id)
                    <div class="mb-3 row">
                        <label for="password" class="form-label col-sm-2 col-form-label required">Password</label>
                        <div class="col-sm-10">
                            <input type="password" minlength="8" maxlength="191" name="password" id="password" placeholder="Current password"
                                   class="form-control @error('password') is-invalid @enderror col-sm-12"
                                   value="" required>
                            @error('password')
                            @include('partials.invalid_feedback')
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="mb-3 row">
                    <label for="new_password" class="form-label col-sm-2 col-form-label">New password</label>
                    <div class="col-sm-10 d-flex">
                        <div class="input-group">
                            <input type="password" minlength="8" maxlength="191" name="new_password" id="new_password"
                               placeholder="New password" class="form-control @error('new_password') is-invalid @enderror col-sm-12"
                               value="">
                            <div class="input-group-text"><span id="toggle-password" class="fas fa-eye fa-fw"></span>
                            </div>
                            <div class="input-group-text"><span id="generate-password" class="fas fa-magic fa-fw"></span>
                            </div>
                        </div>
                        @error('new_password')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->hasPermission('admin') && Auth::user()->id !== $user->id && $user->id !== 1)
                    <div class="mb-3 row">
                        <label for="role" class="form-label col-sm-2 col-form-label">Role</label>
                        <div class="col-sm-10">
                            <select id="role" class="form-select @error('role') is-invalid @enderror" name="role">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $role->id === $user->role_id ? ' selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                            @include('partials.invalid_feedback')
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="d-grid gap-2 max-auto">
                    <button type="submit" id="submit" class="btn btn-lg btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
