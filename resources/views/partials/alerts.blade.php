<!-- Start alerts -->
@if(session('success') || isset($success))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{$success ?? session('success')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('warning') || isset($warning))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{$warning ?? session('warning')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error') || isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{$error ?? session('error')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('errors'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach (session('errors')->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
