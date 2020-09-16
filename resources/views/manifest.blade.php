<?php
$base_url = preg_replace('/public$/', '', dirname($_SERVER['PHP_SELF']));
$base_logo = $base_url . (config('settings.logo') ? 'storage/img/logo/' . substr(config('settings.logo'), 0, -4) : 'img/logo/PizzaReader');
?>
{
    "short_name": "{{ config('settings.reader_name') }}",
    "name": "{{ config('settings.reader_name_long') }}",
    "description": "{{ config('settings.description') }}",
    "background_color": "#f8fafc",
    "theme_color": "#1d68a7",
    "start_url": "{{ $base_url }}",
    "scope": "{{ $base_url }}",
    "display": "standalone",
    "icons": [
        {
            "src": "{{ $base_logo . '-256.png' }}",
            "type": "image/png",
            "sizes": "256x256"
        },
        {
            "src": "{{ $base_logo . '-192.png' }}",
            "type": "image/png",
            "sizes": "192x192"
        },
        {
            "src": "{{ $base_logo . '-128.png' }}",
            "type": "image/png",
            "sizes": "128x128"
        },
        {
            "src": "{{ $base_logo . '-96.png' }}",
            "type": "image/png",
            "sizes": "96x96"
        },
        {
            "src": "{{ $base_logo . '-72.png' }}",
            "type": "image/png",
            "sizes": "72x72"
        }
    ]
}
