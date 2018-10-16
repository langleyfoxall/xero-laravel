<?php

namespace LangleyFoxall\XeroLaravel\Apps;

use BadMethodCallException;
use LangleyFoxall\XeroLaravel\Wrappers\QueryWrapper;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Contact;

/**
 * Class PrivateXeroApp
 * @package LangleyFoxall\XeroLaravel
 */
class PrivateXeroApp extends PrivateApplication
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
     * PrivateXeroApp constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);


    }

    /**
     * Call a relationship method, and return a QueryWrapper.
     * Syntax: $xero->contacts()
     *
     * @param $name
     * @param $arguments
     * @return QueryWrapper
     * @throws \XeroPHP\Remote\Exception
     */
    public function __call($name, $arguments)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (!in_array($name, $relationships)) {
            throw new BadMethodCallException();
        }

        $model = $this->relationshipToModelMap[$name];

        return new QueryWrapper($this->load($model));
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

        return $this->$name()->get();
    }
}

