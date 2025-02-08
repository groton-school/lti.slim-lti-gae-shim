<?php

declare(strict_types=1);

namespace GrotonSchool\SlimLTI\GAE\Infrastructure;

use App\Infrastructure\LTI\Firestore\FirestoreRegistration;
use Packback\Lti1p3\Interfaces\IDatabase;
use Google\Cloud\Firestore\FirestoreClient;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\OidcException;

/**
 * @see https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#database Working from Packback's wiki example
 */
class Database implements IDatabase
{
    private FirestoreClient $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreClient();
    }

    /**
     * @throws OidcException If multiple `issuer` matches without `clientId`
     */
    public function findRegistration(
        string $issuer,
        ?string $clientId = null
    ): ?FirestoreRegistration {
        $query = $this->firestore
            ->collection(FirestoreRegistration::COLLECTION_PATH)
            ->where('issuer', '=', $issuer);
        if ($clientId) {
            $query = $query->where('client_id', '=', $clientId);
        }
        $result = $query->documents();
        if ($result->size() > 1) {
            throw new OidcException(
                'Found multiple registrations for the given issuer, ensure a client_id is specified on login (contact your LMS administrator)',
                1
            );
        }
        if ($result->size() == 1) {
            $registration = $result->rows()[0]->data();
            return FirestoreRegistration::new()
                ->setAuthTokenUrl($registration['auth_token_url'])
                ->setAuthLoginUrl($registration['auth_login_url'])
                ->setClientId($registration['client_id'])
                ->setKeySetUrl($registration['key_set_url'])
                // ->setKid($registration['kid'])
                ->setIssuer($registration['issuer'])
                ->setToolPrivateKey($registration['tool_private_key'])
                ->setDeployments($registration['deployments']);
        }
        return false;
    }

    public function findRegistrationByIssuer(
        string $iss,
        ?string $clientId = null
    ): ?ILtiRegistration {
        return $this->findRegistration($iss, $clientId);
    }

    public function findDeployment(
        string $issuer,
        string $deploymentId,
        ?string $clientId = null
    ): ?ILtiDeployment {
        $registration = $this->findRegistration($issuer, $clientId);
        if (!$registration) {
            return false;
        }
        if ($registration->hasDeployment($deploymentId)) {
            return LtiDeployment::new($deploymentId);
        }
        return false;
    }
}
