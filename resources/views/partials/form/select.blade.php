@extends('partials.form.base')
@section('element-' . $field)
        <select {{ isset($required) && $required ? 'required' : '' }} class="form-control @error($field) is-invalid @enderror"
                name="{{ $field }}" id="{{ $field }}">
        @if(isset($nullable))
            <option value="0">---</option>
        @endif
        @foreach($options as $option)
            <?php
                $option_selected = '';
                if (is_string($option)) {
                    $option_value = $option;
                    $option_name = $option;
                } else {
                    $option_value = $option->id;
                    $option_name = $option->name;
                }
                if ($__env->yieldContent($field) == $option_value || ($__env->yieldContent($field) === '' && isset($selected) && $selected === $option_value)) {
                    $option_selected = 'selected';
                }
            ?>
            <option value="{{ $option_value }}" {{ $option_selected }}>{{ $option_name }}</option>
        @endforeach
        </select>
@endsection
