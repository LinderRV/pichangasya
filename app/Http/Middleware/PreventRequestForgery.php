<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;

class PreventRequestForgery extends Middleware
{
    /**
     * The frontend sends the CSRF token in forms, meta tags and X-CSRF-TOKEN
     * headers. It does not need the JavaScript-readable XSRF-TOKEN cookie.
     */
    protected $addHttpCookie = false;
}
