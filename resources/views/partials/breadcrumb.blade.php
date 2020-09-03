<nav aria-label="breadcrumb">
                <ol class="breadcrumb">
        <?php $link = "/admin" ?>
            <li class="breadcrumb-item"><a href="{{ $link }}">Admin</a></li>
        @for($i = 2; $i <= count(Request::segments()); $i++)
            @if(Request::segment($i-1) === "comics" && !in_array(Request::segment($i), forbidden_words)) <?php $text = $comic->name; ?>
            @elseif(Request::segment($i-1) === "chapters" && !in_array(Request::segment($i), forbidden_words)) <?php $text = \App\Chapter::name($chapter); ?>
            @else <?php $text = ucwords(Request::segment($i)) ?>
            @endif
            @if($i < count(Request::segments()))
        <li class="breadcrumb-item">
                <?php $link .= "/" . Request::segment($i); ?>
            <a href="{{ $link }}">{{ $text }}</a>
            @else
        <li class="breadcrumb-item active" aria-current="page">{{ $text }}
            @endif
        </li>
        @endfor
        </ol>
            </nav>
