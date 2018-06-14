# GrantId Provider for OAuth 2.0 Client

[Latest Version](https://packagist.org/packages/grant-software/oauth2-grantid)
[Software License](https://github.com/grant-software/oauth2-grantid/blob/master/LICENSE)

This package provides GrantId OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client), v2.0 and up.

## Installation

To install, use composer:

```bash
composer require grant-software/oauth2-grantid
```

## Usage

Usage is the same as The League's OAuth 2.0 client, using `GrantId\OAuth2\Client\Provider\GrantId` as the provider.

### Hybrid Flow

This example retrieves an authorization code using user credentials in order to exchange for access tokens.

```php
if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    var_dump($authUrl);
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {
        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo 'Access Token: ' . $accessToken->getToken() . "<br>";
        echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
        echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
        echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());

        // The provider provides a way to get an authenticated API request for
        // the service, using the access token; it returns an object conforming
        // to Psr\Http\Message\RequestInterface.
        $request = $provider->getAuthenticatedRequest(
            'GET',
            'http://brentertainment.com/oauth2/lockdin/resource',
            $accessToken
        );
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());
    }
}
```

## Refreshing a Token

To obtain an refresh token, you must have requested a token with the offline_access scope. The following example shows 
how to use the provider to refresh the token.

```php

$provider = new GrantId\OAuth2\Client\Provider\GrantId([
    'clientId'     => '{client-id}',
    'clientSecret' => '{client-secret}',
    'redirectUri'  => 'https://mysite.com/callback',
    'scopes' => '{scopes} offline_access',
    // Your subscription url 
    'authority'    => 'https://sub.grantid.com'
]);

$existingAccessToken = getAccessTokenFromYourDataStore();

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);

    // Purge old access token and store new access token to your data store.
}

```