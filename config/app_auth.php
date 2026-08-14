<?php

/*
|--------------------------------------------------------------------------
| Hardcoded application credentials
|--------------------------------------------------------------------------
|
| The technical test specifies a single fixed username/password rather
| than a full user-registration system, so it is kept here as config
| (sourced from .env) instead of a users table.
|
*/

return [
    'username' => env('AUTH_USERNAME', 'aldmic'),
    'password' => env('AUTH_PASSWORD', '123abc123'),
];
