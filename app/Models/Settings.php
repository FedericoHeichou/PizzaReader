<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {
    protected $fillable = [
        'value',
    ];

    public static function getSocials() {
        return Settings::where('key', 'like', 'social_%')->whereNotNull('value')->get();
    }

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
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'reader_name_long',
                    'label' => 'Long name of reader',
                    'hint' => 'Insert the long name of the reader. It is used for SEO',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'description',
                    'label' => 'Description of reader',
                    'hint' => 'Insert the description of the reader. It is used for SEO',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_file',
                'parameters' => [
                    'field' => 'logo',
                    'label' => 'Logo',
                    'hint' => 'Insert the logo of the reader. It is showed in the navigation bar (ONLY PNG)',
                    'accept' => '.png'
                ],
                'values' => ['file', 'mimes:png', 'max:10240'],
            ], [
                'type' => 'input_file',
                'parameters' => [
                    'field' => 'cover',
                    'label' => 'Cover',
                    'hint' => 'Insert the cover of the reader. It could be used as preview in other websites',
                    'accept' => '.jpg,.jpeg,.png,.gif,.webp'
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
                    'hint' => 'Check to enable the direct download of a volume. [IMPORTANT: Max cache download need to be high if this option is enabled, else malformed volume zips can be generated]',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'max_cache_download',
                    'label' => 'Max cache download (MB)',
                    'hint' => 'When this limit is reached less downloaded ZIPs are deleted. 0 is infinite. [IMPORTANT: if you enable download volume keep this number high]',
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'pdf_chapter',
                    'label' => 'Download PDF of chapter',
                    'hint' => 'Check to enable the direct download of a PDF of this chapter. [IMPORTANT: you must have Imagick PHP extension installed else this option will still be disabled even though this is checked]',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'max_cache_pdf',
                    'label' => 'Max cache download for PDF (MB)',
                    'hint' => 'When this limit is reached less downloaded PDFs are deleted. 0 is infinite.',
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'footer',
                    'label' => 'Footer text',
                    'hint' => 'The text showed in the footer',
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'registration_enabled',
                    'label' => 'User registration',
                    'hint' => 'Check to enable the registration of new users',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'default_hidden_comic',
                    'label' => 'Set comics hidden as default',
                    'hint' => 'Check to set the hidden checkbox checked as default in the comic\'s creation',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'default_hidden_chapter',
                    'label' => 'Set chapters hidden as default',
                    'hint' => 'Check to set the hidden checkbox checked as default in the chapter\'s creation',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'default_chapter_thumbnail',
                    'label' => 'Set first page as default chapter thumbnail',
                    'hint' => 'The thumbnail showed when a chapter is linked in socials. If unchecked the comic\'s thumbnail is used',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_facebook',
                    'label' => 'Facebook',
                    'hint' => 'Insert the URL of your Facebook page',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_instagram',
                    'label' => 'Instagram',
                    'hint' => 'Insert the URL of your Instagram page',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_twitter',
                    'label' => 'Twitter',
                    'hint' => 'Insert the URL of your Twitter account',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_telegram_channel',
                    'label' => 'Telegram channel',
                    'hint' => 'Insert the URL of your Telegram channel',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_telegram_group',
                    'label' => 'Telegram group',
                    'hint' => 'Insert the URL of your Telegram group',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_telegram_bot',
                    'label' => 'Telegram bot',
                    'hint' => 'Insert the URL of your Telegram bot',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'social_discord',
                    'label' => 'Discord',
                    'hint' => 'Insert the URL of your Discord server',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'menu',
                    'label' => 'Menu tabs',
                    'hint' => 'Each line is an additional menu tab. [Format: Name=css,url (Project=fab fa-github fa-fw,https://github.com/FedericoHeichou/PizzaReader)]',
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'homepage_html',
                    'label' => 'Homepage HTML',
                    'hint' => 'It is showed on the right side of the homepage. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'all_comics_top_html',
                    'label' => 'All Comics top HTML',
                    'hint' => 'It is showed above the list of comics in the "All Comics" tab. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'all_comics_bottom_html',
                    'label' => 'All Comics bottom HTML',
                    'hint' => 'It is showed below the list of comics in the "All Comics" tab. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'comic_top_html',
                    'label' => 'Comic top HTML',
                    'hint' => 'It is showed above "Chapters" in the Comic tab. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'comic_bottom_html',
                    'label' => 'Comic bottom HTML',
                    'hint' => 'It is showed below "Chapters" in the Comic tab. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'reader_html',
                    'label' => 'Reader HTML',
                    'hint' => 'It is showed below reader\'s controllers. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'banner_top',
                    'label' => 'Banner top',
                    'hint' => 'It is showed above a scan. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'banner_bottom',
                    'label' => 'Banner bottom',
                    'hint' => 'It is showed below a scan. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'footer_html',
                    'label' => 'Footer HTML',
                    'hint' => 'It is showed to the bottom of every page. [IMPORTANT: enable (and disable back) this option from your .env file (remember to purge the cache)]',
                    'disabled' => !config('app.edit_custom_html'),
                    'prohibited' => !config('app.edit_custom_html'),
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'cache_proxy_enabled',
                    'label' => 'Cache proxy',
                    'hint' => 'Permits to proxies to cache some endpoints [BETA]',
                ],
                'values' => ['boolean'],
            ], /*[
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
            ],*/
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
        if (isset($fields['cover']) && $fields['cover']) {
            $fields['cover'] = preg_replace("/%/", "", $request->file('cover')->getClientOriginalName());
        } else {
            unset($fields['cover']);
        }

        $custom_html_fields = [
            'homepage_html', 'all_comics_top_html', 'all_comics_bottom_html', 'comic_top_html', 'comic_bottom_html',
            'reader_html', 'banner_top', 'banner_bottom', 'footer_html',
        ];
        foreach ($custom_html_fields as $field) {
            if (array_key_exists($field, $fields) && !config('app.edit_custom_html')) {
                unset($fields[$field]);
            }
        }
        return $fields;
    }

    public static function getFieldsIfValid($request) {
        $form_fields = Settings::getFormFieldsForValidation();
        $request->validate($form_fields);
        return Settings::getFieldsFromRequest($request, $form_fields);
    }

}
