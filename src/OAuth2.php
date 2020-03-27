<?php

namespace LangleyFoxall\XeroLaravel;

use Calcinai\OAuth2\Client\Provider\Xero as Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidConfigException;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidOAuth2StateException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use function compact;
use function config;
use function redirect;
use function session;

class OAuth2
{
    const KEYS = [
        'SESSION_STATE' => 'oauth2state',
        'REQUEST_STATE' => 'state',
        'REQUEST_CODE'  => 'code',
    ];

    /** @var Request $request */
    protected $request;

    /** @var string $key */
    protected $key;

    /** @var string $clientId */
    protected $clientId;

    /** @var string $clientSecret */
    protected $clientSecret;

    /** @var string $redirectUri */
    protected $redirectUri;

    /** @var string $scope */
    protected $scope;

    /** @var Provider $provider */
    protected $provider;

    /** @var AccessTokenInterface $token */
    protected $token;

    /**
     * @param Request $request
     * @param string  $key
     */
    public function __construct(Request $request, string $key = 'default')
    {
        $this->request = $request;
        $this->key = $key;

        $this->bootstrap();
    }

    /**
     * Bootstrap the OAuth2 flow by using config values.
     *
     * @return void
     */
    protected function bootstrap()
    {
        $this->startSession();

        $key = $this->key;
        $config = config(Constants::CONFIG_KEY);

        if (isset($config['apps'][$key])) {
            $app = $config['apps'][$key];

            $this->setClientId($app['client_id'] ?? '');
            $this->setClientSecret($app['client_secret'] ?? '');
            $this->setRedirectUri($app['redirect_uri'] ?? '');
            $this->setScope($app['scope'] ?? '');
        }
    }

    /**
     * Get the OAuth2 provider.
     *
     * @param bool $new
     * @return Provider
     * @throws InvalidConfigException
     */
    public function getProvider($new = false)
    {
        if (! empty($provider = $this->provider) && ! $new) {
            return $provider;
        }

        $this->validateConfig();

        return $this->provider = new Provider([
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri'  => $this->redirectUri,
        ]);
    }

    /**
     * Get the authentication URL.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getAuthUri()
    {
        $provider = $this->getProvider();
        $scope = $this->scope;

        return $provider->getAuthorizationUrl(compact(
            'scope'
        ));
    }

    /**
     * Get the token.
     *
     * @param bool $new
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     * @throws InvalidConfigException
     * @throws InvalidOAuth2StateException
     */
    public function getToken($new = false)
    {
        if (! empty($token = $this->token) && ! $new) {
            return $token;
        }

        $provider = $this->getProvider();
        $request = $this->request;

        $sessionState = session()->get(self::KEYS['SESSION_STATE']);
        $requestState = $request->get(self::KEYS['REQUEST_STATE']);
        $code = $request->get(self::KEYS['REQUEST_CODE']);

        // Check that state hasn't been tampered with
        if ((empty($requestState) || ($requestState !== $sessionState))) {
            unset($sessionState);

            throw new InvalidOAuth2StateException;
        }

        return $this->token = $provider->getAccessToken('authorization_code', compact(
            'code'
        ));
    }

    /**
     * Handle the redirect flow for OAuth2.
     *
     * @return bool|RedirectResponse|Redirector
     * @throws InvalidConfigException
     */
    public function redirect()
    {
        $request = $this->request;

        if (! empty($request->get(self::KEYS['REQUEST_CODE']))) {
            return false;
        }

        $authUri = $this->getAuthUri();

        session()->put(
            self::KEYS['SESSION_STATE'],
            $this->getProvider()->getState()
        );

        return redirect($authUri);
    }

    /**
     * Start a session if one has not already been started.
     *
     * @return void
     */
    protected function startSession()
    {
        if (! session()->isStarted()) {
            session()->start();
        }
    }

    /**
     * Validate OAuth2 configuration settings.
     *
     * @return void
     * @throws InvalidConfigException
     */
    protected function validateConfig()
    {
        $key = $this->key;

        if (empty($this->clientId)) {
            throw new InvalidConfigException('A client ID is required for the OAuth2 flow. Set `client_id` in '.$key.' app\'s configuration or call `setClientId` before `redirect`.');
        }

        if (empty($this->clientSecret)) {
            throw new InvalidConfigException('A client secret is required for the OAuth2 flow. Set `client_secret` in '.$key.' app\'s configuration or call `setClientSecret` before `redirect`.');
        }

        if (empty($this->redirectUri)) {
            throw new InvalidConfigException('A redirect URI is required for the OAuth2 flow. Set `redirect_uri` in '.$key.' app\'s configuration or call `setRedirectUri` before `redirect`.');
        }

        if (empty($this->scope)) {
            throw new InvalidConfigException('A scope is required for the OAuth2 flow. Set `scope` in '.$key.' app\'s configuration or call `setScope` before `redirect`.');
        }
    }
}
