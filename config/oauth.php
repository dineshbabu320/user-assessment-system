<?php

return [

    /*
     * The location of the VATSIM OAuth interface
     */
    'base' => env('OAUTH_URL', 'https://auth.vatsim.net'),

    /*
     * The consumer key for your organisation
     */
    'id' => env('OAUTH_ID'),

    /*
     * The secret key for your organisation
     * Do not give this to anyone else or display it to your users. It must be kept server-side
     */
    'secret' => env('OAUTH_SECRET'),

    /**
     * The scopes the user will be requested
     */
    'scopes' => explode(',', env('OAUTH_SCOPES', 'full_name,email,vatsim_details')),

];