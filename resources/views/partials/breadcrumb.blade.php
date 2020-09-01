<nav aria-label="breadcrumb">
                <ol class="breadcrumb">
        <?php $link = "/admin" ?>
            <li class="breadcrumb-item"><a href="{{ $link }}">Admin</a></li>
        @for($i = 2; $i <= count(Request::segments()); $i++)
            @if($i < count(Request::segments()))
        <li class="breadcrumb-item">
                <?php $link .= "/" . Request::segment($i); ?>
                @if(is_int(Request::segment($i)) && Request::segment($i-1) === "comics") <?php $text = $comic->name; ?>
                @elseif(is_int(Request::segment($i)) && Request::segment($i-1) === "chapter") <?php $text = $chapter->title; ?>
                @else <?php $text = ucwords(Request::segment($i)) ?>
                @endif
            <a href="{{ $link }}">{{ $text }}</a>
            @else
        <li class="breadcrumb-item active" aria-current="page">{{ ucwords(Request::segment($i)) }}
            @endif
        </li>
        @endfor
        </ol>
            </nav>
