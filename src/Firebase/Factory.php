<?php

namespace Firebase;

use Firebase\Auth\Token\Handler as TokenHandler;
use Firebase\Exception\InvalidArgumentException;
use Firebase\Exception\LogicException;
use Firebase\V3\Firebase;
use Google\Auth\CredentialsLoader;
use GuzzleHttp\Psr7;
use Psr\Http\Message\UriInterface;

final class Factory
{
    const ENV_VAR = 'FIREBASE_CREDENTIALS';

    /**
     * @var string[]
     */
    private $credentialPaths;

    /**
     * @var UriInterface
     */
    private $databaseUri;

    /**
     * @var TokenHandler
     */
    private $tokenHandler;

    private static $databaseUriPattern = 'https://%s.firebaseio.com';

    public function __construct()
    {
        $this->setupDefaults();
    }

    public function withCredentials($path)
    {
        $factory = clone $this;
        array_unshift($factory->credentialPaths, $path);

        return $factory;
    }

    public function withDatabaseUri($uri)
    {
        $factory = clone $this;
        $factory->databaseUri = Psr7\uri_for($uri);

        return $factory;
    }

    public function withTokenHandler(TokenHandler $handler)
    {
        $factory = clone $this;
        $factory->tokenHandler = $handler;

        return $factory;
    }

    private function setupDefaults()
    {
        $this->credentialPaths = array_filter([
            getenv(self::ENV_VAR),
            getenv(CredentialsLoader::ENV_VAR),
        ]);
    }

    public function create()
    {
        $serviceAccount = $this->getServiceAccount();
        $databaseUri = $this->databaseUri;
        $databaseUri = !is_null($databaseUri) ? $databaseUri : $this->getDatabaseUriFromServiceAccount($serviceAccount);
        $tokenHandler = $this->tokenHandler;
        $tokenHandler = !is_null($tokenHandler) ? $tokenHandler : $this->getDefaultTokenHandler($serviceAccount);

        return new Firebase($serviceAccount, $databaseUri, $tokenHandler);
    }

    private function getDatabaseUriFromServiceAccount(ServiceAccount $serviceAccount)
    {
        return Psr7\uri_for(sprintf(self::$databaseUriPattern, $serviceAccount->getProjectId()));
    }

    private function getServiceAccount()
    {
        if (count($serviceAccounts = $this->getServiceAccountCandidates())) {
            return reset($serviceAccounts);
        }

        // @codeCoverageIgnoreStart
        if ($credentials = CredentialsLoader::fromWellKnownFile()) {
            return ServiceAccount::fromValue($credentials);
        }
        // @codeCoverageIgnoreEnd

        throw new LogicException(sprintf(
            'No service account has been found. Please set the path to a service account credentials file with %s::%s()',
            static::class, 'withCredentials($path)'
        ));
    }

    private function getServiceAccountCandidates()
    {
        return array_filter(array_map(function ($path) {
            try {
                return ServiceAccount::fromValue($path);
            } catch (InvalidArgumentException $e) {
                return null;
            }
        }, $this->credentialPaths));
    }

    private function getDefaultTokenHandler(ServiceAccount $serviceAccount)
    {
        return new TokenHandler(
            $serviceAccount->getProjectId(),
            $serviceAccount->getClientEmail(),
            $serviceAccount->getPrivateKey()
        );
    }
}
