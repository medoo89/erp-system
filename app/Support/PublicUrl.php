<?php

namespace App\Support;

class PublicUrl
{
    public static function route(string $name, array $parameters = []): string
    {
        return rtrim(config('app.public_app_url'), '/') . route($name, $parameters, false);
    }
}