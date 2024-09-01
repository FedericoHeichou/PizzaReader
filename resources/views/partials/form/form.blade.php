@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <h3 class="mt-1 float-start">@yield('card-title')</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="@yield('form-action')" method="POST" enctype="multipart/form-data">
                @csrf
                @yield('method')
                @foreach($fields as $field)
                    <?php $field['parameters']['type'] =  $field['type'] ?>
                    @include('partials.form.' . $field['type'], $field['parameters'])
                @endforeach
                <div class="d-grid gap-2 max-auto">
                    <button type="submit" id="submit" class="btn btn-lg btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
