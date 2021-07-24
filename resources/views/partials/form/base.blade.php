@if($type !== "input_hidden")
    <div class="form-group form-row">
        <label for="{{ $field }}" class="col-sm-2 col-form-label {{ isset($required) && $required ? 'required' : '' }}">{{ $label }}</label>
        <div class="col-sm-10 {{ (isset($disabled) && $disabled) || (isset($clear) && $clear) ? 'inline-components' : '' }}">
            @yield('element-' . $field)
            @if(isset($disabled) && $disabled && isset($edit) && $edit)
                <div class="btn btn-lg btn-success btn-block col-sm-2" onclick="event.preventDefault();$('#{{ $field }}').prop('disabled', function(i, v) { return !v; });let text =$(this).text();$(this).text(text === 'Edit' ? 'Undo' : 'Edit').toggleClass('btn-success').toggleClass('btn-danger');">Edit</div>
            @endif
            @if(isset($clear) && $clear)
                <div class="btn btn-lg btn-danger btn-block col-sm-2" onclick="event.preventDefault();$('#{{ $field }}').val('');">Clear</div>
            @endif
            @error($field)
            @include('partials.invalid_feedback')
            @enderror
            <small class="form-text text-muted">
                {{ $hint }}
            </small>
        </div>
    </div>
@else
    @yield('element-' . $field)
@endif
