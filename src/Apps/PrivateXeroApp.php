<?php

namespace LangleyFoxall\XeroLaravel\Apps;

use Exception;
use LangleyFoxall\XeroLaravel\Traits\HasXeroRelationships;
use XeroPHP\Application\PrivateApplication;

/**
 * Class PrivateXeroApp
 *
 * @package LangleyFoxall\XeroLaravel
 */
class PrivateXeroApp extends PrivateApplication
{
    use HasXeroRelationships;

    /**
     * PrivateXeroApp constructor.
     *
     * @param $config
     * @throws Exception
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->populateRelationshipToModelMaps();
    }
}
