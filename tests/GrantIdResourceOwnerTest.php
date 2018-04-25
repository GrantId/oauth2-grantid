<?php


use GrantId\OAuth2\Client\Provider\GrantIdResourceOwner;
use PHPUnit\Framework\TestCase;

class Auth0ResourceOwnerTest extends TestCase
{
    public $response = [
        'email' => 'testuser@mailinator.com',
        'name' => 'User',
        'sub' => '443ead2f-68dc-4592-8179-5841e44f628a',
        'phone' => '+55 (00) 00000-0000',
        'custom' => 'Custom Info'
    ];

    public function testGetUserDetails()
    {
        $user = new GrantIdResourceOwner($this->response);
        $this->assertEquals($this->response['name'], $user->getName());
        $this->assertEquals($this->response['sub'], $user->getId());
        $this->assertEquals($this->response['email'], $user->getEmail());
        $this->assertEquals($this->response['phone'], $user->getPhone());
        $this->assertEquals($this->response['custom'], $user->getInfo('custom'));
        $this->assertEquals($this->response, $user->toArray());
    }
}