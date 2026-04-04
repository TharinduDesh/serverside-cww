<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SecurityHeaders
{
    public function apply_headers()
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
        header("X-XSS-Protection: 1; mode=block");
    }
}