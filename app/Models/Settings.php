<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {
    protected $fillable = [
        'value',
    ];

    public static function getFormFields() {
        return [
            [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'reader_name',
                    'label' => 'Name of reader',
                    'hint' => 'Insert the name of the reader. It is showed in the navigation bar',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_file',
                'parameters' => [
                    'field' => 'logo',
                    'label' => 'Logo',
                    'hint' => 'Insert the logo of the reader. It is showed in the navigation bar',
                ],
                'values' => ['file', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'home_link',
                    'label' => 'Home link',
                    'hint' => 'Insert the URL of your principal website',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'default_language',
                    'label' => 'Default language',
                    'hint' => 'Select the default language for future chapter releases',
                    'options' => ['en', 'es', 'fr', 'it', 'pt', 'jp',],
                    'selected' => config('settings.default_language'),
                    'required' => 'required',
                ],
                'values' => ['string', 'size:2'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'download_chapter',
                    'label' => 'Download chapter',
                    'hint' => 'Check to enable the direct download of a chapter',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'download_volume',
                    'label' => 'Download volume',
                    'hint' => 'Check to enable the direct download of a volume',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'max_cache_download',
                    'label' => 'Max cache download (MB)',
                    'hint' => 'When this limit is reached less downloaded ZIPs are deleted. 0 is infinite',
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'footer',
                    'label' => 'Footer text',
                    'hint' => 'The text showed in the footer',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'registration_enabled',
                    'label' => 'User\'s registration',
                    'hint' => 'Check to enable the registration of new users',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'recaptcha_public',
                    'label' => 'reCAPTCHA v3 public',
                    'hint' => 'The public key of your reCAPTCHA v3',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'recaptcha_private',
                    'label' => 'reCAPTCHA v3 private',
                    'hint' => 'The private key of your reCAPTCHA v3',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'adsense_publisher',
                    'label' => 'AdSense publisher id',
                    'hint' => 'Usually starts with "pub" or "ca-pub". If this is empty your reader will be ad-free',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'banner_top',
                    'label' => 'AdSense banner top',
                    'hint' => 'Insert the code of your adsense banner (auto resizeable)',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'banner_bottom',
                    'label' => 'AdSense banner bottom',
                    'hint' => 'Insert the code of your adsense banner (auto resizeable)',
                ],
                'values' => ['max:191'],
            ],
        ];
    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Settings::getFormFields());
    }

    public static function getFieldsFromRequest($request, $form_fields) {
        $fields = getFieldsFromRequest($request, $form_fields);
        if (isset($fields['logo']) && $fields['logo']) {
            $fields['logo'] = preg_replace("/%/", "", $request->file('logo')->getClientOriginalName());
        } else {
            unset($fields['logo']);
        }
        return $fields;
    }

    public static function getFieldsIfValid($request) {
        $form_fields = Settings::getFormFieldsForValidation();
        $request->validate($form_fields);
        return Settings::getFieldsFromRequest($request, $form_fields);
    }

}
