<?php
namespace LangleyFoxall\XeroLaravel;

use Exception;
use LangleyFoxall\XeroLaravel\Apps\PrivateXeroApp;
use LangleyFoxall\XeroLaravel\Apps\PublicXeroApp;
use XeroPHP\Application;

class Xero
{
    private $apps = [];

    /**
     * Get XeroApp object
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function app($key = 'default')
    {
        if (!isset($this->apps[$key])) {
            $this->apps[$key] = $this->createApp($key);
        }

        return $this->apps[$key];
    }

    /**
     * Creates the XeroApp object
     *
     * @param string $key
     * @return Application
     * @throws Exception
     */
    private function createApp($key)
    {
        $config = config(Constants::CONFIG_KEY);

        if (!isset($config['apps']) || !isset($config['apps'][$key])) {
            throw new Exception('The specified key could not be found in the Xero \'apps\' array, ' .
                'or the \'apps\' array does not exist.');
        }

        $appConfig = $config['apps'][$key];

        switch ($appConfig['app_type']) {
            case 'private':
                return new PrivateXeroApp($appConfig);
                break;

            case 'public':
                return new PublicXeroApp($appConfig);
                break;

            case 'partner':
                throw new Exception('Partner Xero app types are not yet supported.');
                break;
        }

        throw new Exception('Xero app type is invalid. Should be \'private\', \'public\', or \'partner\'.');
    }
}
