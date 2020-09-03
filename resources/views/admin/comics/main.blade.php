@extends('layouts.admin')
@section('content')
    @yield('information')
    <div class="card">
        <div class="card-header">
            <div class="form-row">
                <div class="col-9">
                    <h3 class="mt-1 float-left">@yield('list-title')</h3>
                    @if(Auth::user()->hasPermission("manager"))
                        @yield('list-buttons')
                    @endif
                </div>
                <div class="col-3">
                    @include('partials.card-search')
                </div>
            </div>
        </div>
        <div class="card-body">
            @yield('list')
        </div>
    </div>
@endsection
