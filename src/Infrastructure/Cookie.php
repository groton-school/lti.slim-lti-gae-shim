<?php

declare(strict_types=1);

namespace GrotonSchool\SlimLTI\GAE\Infrastructure;

use Packback\Lti1p3\Interfaces\ICookie;

/**
 * @see https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cookie Working from Packback's wiki example
 *
 * FIXME Third party cookie restrictions
 *   There has been
 *   [some discussion](https://stackoverflow.com/a/67688369/294171)
 *   about how to handle the third party cookies inherent in `iframe` resource
 *   placements, with people leaning towards partitioned cookies (the
 *   Chrome/Firefox solution), but referencing the pain of getting a working
 *   work-around for Safari.
 */
class Cookie implements ICookie
{
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name];
    }

    public function setCookie(
        string $name,
        string $value,
        int $exp = 3600,
        array $options = []
    ): void {
        setcookie($name, $value, $exp, ...$options);
    }
}
