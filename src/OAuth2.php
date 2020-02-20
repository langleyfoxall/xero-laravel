<?php

namespace LangleyFoxall\XeroLaravel;

use Calcinai\OAuth2\Client\Provider\Xero as Provider;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidConfigException;
use LangleyFoxall\XeroLaravel\Exceptions\InvalidOAuth2StateException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use function config;

class OAuth2
{
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

    /** @var string $token */
    protected $token;

    /**
     * @param string $key
     */
    public function __construct(string $key = 'default')
    {
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
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Manually set the client ID.
     *
     * @param string $clientId
     * @return $this
     */
    public function setClientId(string $clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Manually set the client secret.
     *
     * @param string $clientSecret
     * @return $this
     */
    public function setClientSecret(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Manually set the redirect URI.
     *
     * @param string $redirectUri
     * @return $this
     */
    public function setRedirectUri(string $redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope(string $scope)
    {
        $this->scope = trim($scope);

        return $this;
    }

    /**
     * Handle the redirect flow for OAuth2.
     *
     * @return $this
     * @throws InvalidConfigException
     * @throws InvalidOAuth2StateException
     * @throws IdentityProviderException
     */
    public function redirect()
    {
        $this->validateConfig();

        $provider = new Provider([
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri'  => $this->redirectUri,
        ]);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Attempt to get token
        if (! isset($_GET['code'])) {
            $scope = $this->scope;

            $authUri = $provider->getAuthorizationUrl(compact(
                'scope'
            ));

            $_SESSION['oauth2state'] = $provider->getState();

            header('Location: '.$authUri);
            exit;
        }

        // Check that state hasn't been tampered with
        if ((empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state']))) {
            unset($_SESSION['oauth2state']);

            throw new InvalidOAuth2StateException;
        }

        // Try to get token from grant
        $this->token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code'],
        ]);

        return $this;
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
