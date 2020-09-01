<!-- Start alerts -->
@if(session('success') || isset($success))
    <div class="alert alert-success" role="alert">
        {{$success ?? session('success')}}
    </div>
@endif
@if(session('warning') || isset($warning))
    <div class="alert alert-warning" role="alert">
        {{$warning ?? session('warning')}}
    </div>
@endif
@if(session('errors'))
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach (session('errors')->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
