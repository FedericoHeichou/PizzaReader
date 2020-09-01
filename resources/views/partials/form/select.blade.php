<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ $required ?? '' }}">{{ $label }}</label>
    <div class="col-sm-10">
        <select {{ $required ?? '' }} class="form-control @error($field) is-invalid @enderror"
                name="{{ $field }}" id="{{ $field }}">
        @foreach($options as $option)
            <option value="{{ $option->id }}">{{  $option->name }}</option>
        @endforeach
        </select>
        @error($field)
        @include('partials.invalid_feedback')
        @enderror
        <small class="form-text text-muted">
            {{ $hint }}
        </small>
    </div>
</div>
