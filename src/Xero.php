<?php

namespace LangleyFoxall\XeroLaravel;

class Xero
{
    private $apps = [];

    /**
     * Get Xero application object
     *
     * @return mixed
     * @throws \Exception
     */
    public function app($key = 'default')
    {
        if (!isset($this->$apps[$key])) {
            $this->apps[$key] = $this->createApp($key);
        }

        return $this->apps[$key];
    }

    /**
     * Creates the app from configuration
     *
     * @return XeroPrivateApp
     * @throws \Exception
     */
    private function createApp()
    {
        $config = config(Constants::CONFIG_KEY);

        switch ($config['app_type']) {
            case 'private':
                return new XeroPrivateApp($config);
                break;

            case 'public':
            case 'partner':
                throw new \Exception('Public and partner Xero app types are not yet supported.');
                break;
        }

        throw new \Exception('Xero app type is invalid. Should be \'private\', \'public\', or \'partner\'.');
    }
}
