<?php

namespace LangleyFoxall\XeroLaravel\Wrappers;

use LangleyFoxall\XeroLaravel\Apps\PrivateXeroApp;

class Xero
{
    private $apps = [];

    /**
     * Get Xero application object
     *
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function app($key = 'default')
    {
        if (!isset($this->apps[$key])) {
            $this->apps[$key] = $this->createApp($key);
        }

        return $this->apps[$key];
    }

    /**
     * Creates the app from configuration
     *
     * @return PrivateXeroApp
     * @throws \Exception
     */
    private function createApp($key)
    {
        $config = config(Constants::CONFIG_KEY);

        if (!isset($config['apps']) || !isset($config['apps'][$key])) {
            throw new \Exception('The specified key could not be found in the Xero \'apps\' array, ' .
                'or the \'apps\' array does not exist.');
        }

        $appConfig = $config['apps'][$key];

        switch ($appConfig['app_type']) {
            case 'private':
                return new PrivateXeroApp($appConfig);
                break;

            case 'public':
            case 'partner':
                throw new \Exception('Public and partner Xero app types are not yet supported.');
                break;
        }

        throw new \Exception('Xero app type is invalid. Should be \'private\', \'public\', or \'partner\'.');
    }
}
