<?php

declare(strict_types=1);

namespace GrotonSchool\SlimLTI\GAE\Infrastructure;

use App\Application\Settings\SettingsInterface;
use Packback\Lti1p3\Interfaces\ICache;
use Google\AppEngine\Api\Memcache\Memcache;

/**
 * @see https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache Working from Packback's wiki example
 */
class Cache implements ICache
{
    public const NONCE_PREFIX = 'nonce_';
    public const DURATION = 'duration';

    private Memcache $memcache;
    private int $duration;

    // FIXME SettingsInterface undefined in slim-lti-gae-shim
    public function __construct(SettingsInterface $settings)
    {
        $this->memcache = new Memcache();
        $this->duration = $settings->get(self::class)[self::DURATION];
    }

    public function getLaunchData(string $key): ?array
    {
        return $this->memcache->get($key);
    }

    public function cacheLaunchData(string $key, array $jwtBody): void
    {
        $this->memcache->set($key, $jwtBody, null, $this->duration);
    }

    public function cacheNonce(string $nonce, string $state): void
    {
        $this->memcache->set(
            self::NONCE_PREFIX . $nonce,
            $state,
            null,
            $this->duration
        );
    }

    public function checkNonceIsValid(string $nonce, string $state): bool
    {
        return $this->memcache->get(self::NONCE_PREFIX . $nonce) === $state;
    }

    public function cacheAccessToken(string $key, string $accessToken): void
    {
        $this->memcache->set($key, $accessToken, null, $this->duration);
    }

    public function getAccessToken(string $key): ?string
    {
        return $this->memcache->get($key);
    }

    public function clearAccessToken(string $key): void
    {
        $this->memcache->delete($key);
    }
}
