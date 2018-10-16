<?php

namespace LangleyFoxall\XeroLaravel;

use BadMethodCallException;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Contact;

/**
 * Class XeroPrivateApp
 * @package LangleyFoxall\XeroLaravel
 */
class XeroPrivateApp extends PrivateApplication
{
    /**
     * Map between relationship names and Xero PHP library models
     *
     * @var array
     */
    private $relationshipToModelMap = [
        'contacts' => Contact::class
    ];

    /**
     * Call a relationship method.
     * Syntax: $xero->contacts()
     *
     * @param $name
     * @param $arguments
     * @return \XeroPHP\Remote\Query
     * @throws \XeroPHP\Remote\Exception
     */
    public function __call($name, $arguments)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (!in_array($name, $relationships)) {
            throw new BadMethodCallException();
        }

        $model = $this->relationshipToModelMap[$name];

        return $this->load($model);
    }

    /**
     * Call a relationship method and get results.
     * Syntax: $xero->contacts
     *
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (!in_array($name, $relationships)) {
            return null;
        }

        return $this->$name()->execute();
    }
}

