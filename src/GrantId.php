<?php

namespace GrantId\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;


class GrantId extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $authority;
    protected $scopes;
    private $responseError = 'error';
    private $responseCode;

    public function getBaseAuthorizationUrl()
    {
        return $this->authority . 'connect/authorize';
    }

    public function getBaseAccessTokenUrl(array $params = [])
    {
        return $this->authority . 'connect/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->authority . 'connect/userinfo';
    }

    public function getDefaultScopes()
    {
        return $this->scopes;
    }

    protected function getAccessTokenResourceOwnerId()
    {
        return 'sub';
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data[$this->responseError])) {
            $error = $data[$this->responseError];
            if (!is_string($error)) {
                $error = var_export($error, true);
            }
            $code  = $this->responseCode && !empty($data[$this->responseCode])? $data[$this->responseCode] : 0;
            if (!is_int($code)) {
                $code = intval($code);
            }
            throw new IdentityProviderException($error, $code, $data);
        }
    }
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GrantIdResourceOwner($response);
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }
}