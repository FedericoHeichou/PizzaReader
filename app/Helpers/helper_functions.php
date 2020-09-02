<?php

use Illuminate\Support\Str;

function generateSlug($class, $fields) {
    $fields['slug'] = isset($fields['slug']) ? Str::slug($fields['slug']) : (isset($fields['name']) ? Str::slug($fields['name']) : Str::slug($fields['title']));
    $slug = $fields['slug'];
    $i = 2;
    while ($class::slug($slug)) {
        $slug = $fields['slug'] . '-' . $i;
        $i++;
    }
    return $slug;
}

function getFormFieldsForValidation($fields) {
    $form_fields = [];
    foreach ($fields as $field) {
        $values = $field['values'];
        if (isset($field['parameters']['required'])) {
            array_push($values, 'required');
        } else {
            array_push($values, 'nullable');
        }
        $form_fields[$field['parameters']['field']] = $values;
    }
    return $form_fields;
}
