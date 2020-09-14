<!-- Start alerts -->
@if(session('success') || isset($success))
    <div class="alert alert-success" role="alert">
        {{$success ?? session('success')}}
        <button type="button" class="close" onclick="$(this).parent().hide();"><span aria-hidden="true">×</span>
        </button>
    </div>
@endif
@if(session('warning') || isset($warning))
    <div class="alert alert-warning" role="alert">
        {{$warning ?? session('warning')}}
        <button type="button" class="close" onclick="$(this).parent().hide();"><span aria-hidden="true">×</span>
        </button>
    </div>
@endif
@if(session('error') || isset($error))
    <div class="alert alert-danger" role="alert">
        {{$error ?? session('error')}}
        <button type="button" class="close" onclick="$(this).parent().hide();"><span aria-hidden="true">×</span>
        </button>
    </div>
@endif
@if(session('errors'))
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" onclick="$(this).parent().hide();"><span aria-hidden="true">×</span>
        </button>
        <ul>
            @foreach (session('errors')->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
