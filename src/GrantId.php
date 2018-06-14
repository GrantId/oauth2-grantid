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
    protected $postLogoutUri;
    private $responseError = 'error';
    private $responseCode;

    private function getAuthority() {
        if (empty($this->authority)) {
            throw new \RuntimeException('You need to specify the authority');
        }

        return $this->authority;
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->getAuthority() . 'connect/authorize';
    }
    public function getBaseAccessTokenUrl(array $params = [])
    {
        return $this->getAuthority() . 'connect/token';
    }
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getAuthority() . 'connect/userinfo';
    }

    public function getDiscoveryUrl()
    {
        return $this->getAuthority() . '.well-known/openid-configuration';
    }

    public function getDefaultScopes()
    {
        return $this->scopes;
    }

    private function getLogoutQuery($idToken) {
        return '?id_token_hint=' . $idToken . '&post_logout_redirect_uri='.urlencode($this->postLogoutUri);
    }

    public function getLogoutUrl($idToken) {
        return $this->getAuthority() . 'connect/endsession' . $this->getLogoutQuery($idToken);
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
    protected function getAuthorizationParameters(array $options)
    {
        $params = parent::getAuthorizationParameters($options);

        $params['response_type'] = 'code id_token';
        $params['nonce'] = bin2hex(random_bytes(32 / 2));
        $params['response_mode'] = 'form_post';

        return $params;
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