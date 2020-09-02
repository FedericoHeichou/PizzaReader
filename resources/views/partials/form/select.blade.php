<div class="form-group form-row">
    <label for="{{ $field }}" class="col-sm-2 col-form-label {{ isset($required) ? 'required' : '' }}">{{ $label }}</label>
    <div class="col-sm-10">
        <select {{ isset($required) ? 'required' : '' }} class="form-control @error($field) is-invalid @enderror"
                name="{{ $field }}" id="{{ $field }}">
        @if(isset($nullable))
            <option value="0">---</option>
        @endif
        @foreach($options as $option)
            <option value="{{ $option->id }}" {{ $__env->yieldContent($field) == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
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
