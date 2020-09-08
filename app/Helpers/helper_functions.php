<?php

use Illuminate\Support\Str;

const forbidden_words = ['update', 'edit', 'create', 'show', 'store', 'destroy'];

function generateSlug($class, $fields) {
    $fields['slug'] = isset($fields['slug']) ? Str::slug($fields['slug']) : (isset($fields['name']) ? Str::slug($fields['name']) : Str::slug($fields['title']));
    if (!$fields['slug']) $fields['slug'] = "oneshot";
    $fields['slug'] = substr($fields['slug'], 0, 50);
    $slug = $fields['slug'];
    $i = 2;
    while (in_array($slug, forbidden_words) || (isset($fields['comic_id']) ? $class::slug($fields['comic_id'], $slug) : $class::slug($slug))) {
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

function getFieldsFromRequest($request, $form_fields) {
    $fields = [];
    foreach ($form_fields as $key => $value) {
        if ($request->$key != null) $fields[$key] = $request->$key;
        else $fields[$key] = null;
    }
    return $fields;
}

function trimCommas($string) {
    $string = preg_replace('/\s*,\s*/', ',', $string);
    $string = preg_replace('/,+,+/', ',', $string);
    return trim($string, ',');
}
