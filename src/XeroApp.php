<?php

namespace LangleyFoxall\XeroLaravel;

use Exception;
use LangleyFoxall\XeroLaravel\Traits\HasXeroRelationships;
use XeroPHP\Application;

/**
 * Class XeroApp
 *
 * @package LangleyFoxall\XeroLaravel
 */
class XeroApp extends Application
{
    use HasXeroRelationships;

    /**
     * XeroApp constructor.
     *
     * @param $token
     * @param $tenantId
     * @throws Exception
     */
    public function __construct($token, $tenantId)
    {
        parent::__construct($token, $tenantId);

        $this->populateRelationshipToModelMaps();
    }
}
