<?php

namespace Firebase;

use Firebase\Exception\InvalidArgumentException;
use Firebase\Util\JSON;

class ServiceAccount
{
    private $projectId;
    private $clientId;
    private $clientEmail;
    private $privateKey;

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function withProjectId($value)
    {
        $serviceAccount = clone $this;
        $serviceAccount->projectId = $value;

        return $serviceAccount;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function withClientId($value)
    {
        $serviceAccount = clone $this;
        $serviceAccount->clientId = $value;

        return $serviceAccount;
    }

    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    public function withClientEmail($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid email.', $value));
        }
        $serviceAccount = clone $this;
        $serviceAccount->clientEmail = $value;

        return $serviceAccount;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function withPrivateKey($value)
    {
        $serviceAccount = clone $this;
        $serviceAccount->privateKey = $value;

        return $serviceAccount;
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return ServiceAccount
     */
    public static function fromValue($value)
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_string($value)) {
            try {
                return self::fromJson($value);
            } catch (InvalidArgumentException $e) {
                return self::fromJsonFile($value);
            }
        }

        if (is_array($value)) {
            return self::fromArray($value);
        }

        throw new InvalidArgumentException('Invalid service account specification.');
    }

    private static function fromArray($config)
    {
        if (!isset($config['project_id'], $config['client_id'], $config['client_email'], $config['private_key'])) {
            throw new InvalidArgumentException('Missing/empty values in Service Account Config.');
        }

        return (new self())
            ->withProjectId($config['project_id'])
            ->withClientId($config['client_id'])
            ->withClientEmail($config['client_email'])
            ->withPrivateKey($config['private_key']);
    }

    private static function fromJson($json)
    {
        $config = JSON::decode($json, true);

        return self::fromArray($config);
    }

    private static function fromJsonFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('%s is not readable.', $filePath));
        }

        return self::fromJson(file_get_contents($filePath));
    }
}
