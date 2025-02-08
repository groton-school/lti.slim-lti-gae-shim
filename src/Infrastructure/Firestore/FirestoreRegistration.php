<?php

declare(strict_types=1);

namespace GrotonSchool\SlimLTI\GAE\Infrastructure\Firestore;

use Packback\Lti1p3\LtiRegistration;

/**
 * @method static self new(?array $registration)
 * @method self setAuthTokenUrl(?string $authTokenUrl)
 * @method self setAuthLoginUrl(?string $loginTokenUrl)
 * @method self setClientId(?string $clientId)
 * @method self setKeySetUrl(?string $keySetUrl)
 * @method self setKid(?string $kid)
 * @method self setIssuer(?string $issuer)
 * @method self setToolPrivateKey(?string $toolPrivateKey)
 */
class FirestoreRegistration extends LtiRegistration implements FirestoreObject
{
    public const COLLECTION_PATH = 'lti_registrations';

    /** @var string[] $deployments */
    private array $deployments = [];

    public function name(): string
    {
        return self::COLLECTION_PATH . '/' . $this->getIssuer();
    }

    /**
     * @param string[] $deployments
     */
    public function setDeployments(array $deployments): self
    {
        $this->deployments = $deployments;
        return $this;
    }

    public function hasDeployment(string $deploymentId): bool
    {
        return in_array($deploymentId, $this->deployments);
    }
}
