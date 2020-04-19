<?php

return [
    /*
     |--------------------------------------------------------------------------
     | UUIDs
     |--------------------------------------------------------------------------
     |
     | By default Harness assumes that all your models use UUIDs as primary
     | keys. Firstly, because they're awesome, and secondly, because they
     | work really well with JSON:API
     |
     */

    'use_uuids' => true,

    /*
     |--------------------------------------------------------------------------
     | Require Ids
     |--------------------------------------------------------------------------
     |
     | Setting this option to true will make all ids required. Useful only if
     | use_uuids is set to true.
     |
     */

    'require_ids' => false,

    /*
     |--------------------------------------------------------------------------
     | Model Namespace
     |--------------------------------------------------------------------------
     |
     | Set this to the namespace where your models are available from.
     | Harness then uses this namespace to make some educated guesses
     | about your models from the names of your form requests.
     |
     */

    'model_namespace' => 'App',
];
