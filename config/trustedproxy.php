<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Array of Proxies to trust.
    | If you trust a proxy the x-forwarded-for header will be used as remote IP.
    | Examples: '*', '172.17.0.0/16', '172.17.0.0/16,192.168.1.0/24'
    |
    */

    'proxies' => env('PROXIES', null),

];
