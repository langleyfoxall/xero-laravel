<?php

namespace LangleyFoxall\XeroLaravel;

use Calcinai\OAuth2\Client\Provider\Xero as Provider;
use Calcinai\OAuth2\Client\XeroTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use InvalidArgumentException;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidOAuth2StateException;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidXeroRequestException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OAuth2
{
    const KEYS = [
        'SESSION_STATE' => 'xero-oauth-2-session-state',
    ];

    /** @var string $clientId */
    protected $clientId;

    /** @var string $clientSecret */
    protected $clientSecret;

    /** @var string $redirectUri */
    protected $redirectUri;

    /** @var string $scope */
    protected $scope;

    /**
     * OAuth2 constructor.
     *
     * @param string $key
     */
    public function __construct(string $key = 'default')
    {
        $config = config(Constants::CONFIG_KEY);

        if (!isset($config['apps'][$key])) {
            throw new InvalidArgumentException('Invalid app key specified. Please check your `xero-laravel-lf` configuration file.');
        }

        $app = $config['apps'][$key];

        $this->clientId = $app['client_id'];
        $this->clientSecret = $app['client_secret'];
        $this->redirectUri = $app['redirect_uri'];
        $this->scope = $app['scope'];
    }

    /**
     * Get the OAuth2 provider.
     *
     * @return Provider
     */
    private function getProvider()
    {
        return new Provider([
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri'  => $this->redirectUri,
        ]);
    }

    /**
     * Get a redirect to the Xero authorization URL.
     *
     * @return RedirectResponse|Redirector
     */
    public function getAuthorizationRedirect()
    {
        $provider = $this->getProvider();

        $authUri = $provider->getAuthorizationUrl(['scope' => $this->scope]);

        session()->put(self::KEYS['SESSION_STATE'], $provider->getState());

        return redirect($authUri);
    }

    /**
     * Handle the incoming request from Xero, request an access token and return it.
     *
     * @param Request $request
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     * @throws InvalidOAuth2StateException
     * @throws InvalidXeroRequestException
     */
    public function getAccessTokenFromXeroRequest(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');

        if (!$code) {
            throw new InvalidXeroRequestException('No `code` present in request from Xero.');
        }

        if (!$state) {
            throw new InvalidXeroRequestException('No `state` present in request from Xero.');
        }

        if ($state !== session(self::KEYS['SESSION_STATE'])) {
            throw new InvalidOAuth2StateException('Invalid `state`. Request may have been tampered with.');
        }

        return $this->getProvider()->getAccessToken('authorization_code', ['code' => $code]);
    }

    /**
     * Get all the tenants (typically Xero organisations) that the access token is able to access.
     *
     * @param AccessTokenInterface $accessToken
     * @return XeroTenant[]
     * @throws IdentityProviderException
     */
    public function getTenants(AccessTokenInterface $accessToken)
    {
        return $this->getProvider()->getTenants($accessToken);
    }

    /**
     * Refreshes an access token, and returns the new access token.
     *
     * @param AccessTokenInterface $accessToken
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     */
    public function refreshAccessToken(AccessTokenInterface $accessToken)
    {
        return $this->getProvider()->getAccessToken('refresh_token', [
            'refresh_token' => $accessToken->getRefreshToken()
        ]);
    }
}
