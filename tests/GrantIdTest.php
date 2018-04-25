<?php

namespace GrantId\OAuth2\Client\Test\Provider;


use PHPUnit\Framework\TestCase;
use GrantId\OAuth2\Client\Provider\GrantId;


class GrantIdTest extends TestCase
{
    protected $config = [
        'authority'      => 'https://sub.grantid.com',
        'clientId'     => 'mock_client_id',
        'clientSecret' => 'mock_secret',
        'redirectUri'  => 'none',
    ];

    public function testGetAuthorizationUrl()
    {
        $provider = new GrantId($this->config);
        $url = $provider->getAuthorizationUrl();
        $uri = parse_url($url);
        $this->assertEquals($this->config['authority'], $uri['scheme']. '://' .$uri['host']);
        $this->assertEquals('/connect/authorize', $uri['path']);
    }
    public function testGetAuthorizationUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['authority']);
        $provider = new GrantId($this->config);
        $this->expectException('RuntimeException');
        $provider->getAuthorizationUrl();
    }
    public function testGetUrlAccessToken()
    {
        $provider = new GrantId($this->config);
        $url = $provider->getBaseAccessTokenUrl();
        $uri = parse_url($url);
        $this->assertEquals($this->config['authority'], $uri['scheme']. '://' .$uri['host']);
        $this->assertEquals('/connect/token', $uri['path']);
    }
    public function testGetAccessTokenUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['authority']);
        $provider = new GrantId($this->config);
        $this->expectException('RuntimeException');
        $provider->getBaseAccessTokenUrl();
    }
    public function testGetUrlUserDetails()
    {
        $provider = new GrantId($this->config);
        $accessTokenDummy = $this->getAccessToken();
        $url = $provider->getResourceOwnerDetailsUrl($accessTokenDummy);
        $uri = parse_url($url);
        $this->assertEquals($this->config['authority'], $uri['scheme']. '://' .$uri['host']);
        $this->assertEquals('/connect/userinfo', $uri['path']);
    }
    /**
     * @expectedException \RuntimeException
     */
    public function testGetUserDetailsUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['authority']);
        $provider = new GrantId($this->config);
        $accessTokenDummy = $this->getAccessToken();
        $provider->getResourceOwner($accessTokenDummy);
    }
    private function getAccessToken()
    {
        return $this->getMockBuilder('League\OAuth2\Client\Token\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();
    }
}