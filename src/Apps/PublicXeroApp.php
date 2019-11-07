<?php

namespace LangleyFoxall\XeroLaravel\Apps;

use Exception;
use LangleyFoxall\XeroLaravel\Traits\HasXeroRelationships;
use XeroPHP\Application\PublicApplication;

/**
 * Class PublicXeroApp
 *
 * @package LangleyFoxall\XeroLaravel
 */
class PublicXeroApp extends PublicApplication
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
