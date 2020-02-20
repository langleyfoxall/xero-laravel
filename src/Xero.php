<?php
namespace LangleyFoxall\XeroLaravel;

use Exception;

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
        if (! isset($this->apps[$key])) {
            $this->apps[$key] = $this->createApp($key);
        }

        return $this->apps[$key];
    }

    /**
     * Creates the XeroApp object
     *
     * @param string $key
     * @return XeroApp
     * @throws Exception
     */
    private function createApp($key)
    {
        $config = config(Constants::CONFIG_KEY);

        if (! isset($config['apps'][$key])) {
            throw new Exception('The specified key could not be found in the Xero \'apps\' array, ' .
                'or the \'apps\' array does not exist.');
        }

        return new XeroApp($config['apps'][$key]['token'], $config['apps'][$key]['tenant_id']);
    }
}
