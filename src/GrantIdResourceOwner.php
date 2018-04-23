<?php

namespace GrantId\OAuth2\Client\Provider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;


class GrantIdResourceOwner implements ResourceOwnerInterface
{
    protected $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->response['sub'];
    }

    public function getName()
    {
        return $this->response['name'];
    }

    public function getEmail()
    {
        return $this->response['email'];
    }

    public function getPhone()
    {
        return $this->response['phone'];
    }

    public function getInfo($info)
    {
        return $this->response[$info];
    }

    public function toArray()
    {
        return $this->response;
    }
}