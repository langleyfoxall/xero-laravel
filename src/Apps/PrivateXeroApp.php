<?php

namespace LangleyFoxall\XeroLaravel\Apps;

use BadMethodCallException;
use Exception;
use Illuminate\Support\Str;
use LangleyFoxall\XeroLaravel\Utils;
use LangleyFoxall\XeroLaravel\Wrappers\QueryWrapper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use XeroPHP\Application\PrivateApplication;

/**
 * Class PrivateXeroApp.
 */
class PrivateXeroApp extends PrivateApplication
{
    /**
     * Map between relationship names and Xero PHP library models.
     *
     * @var array
     */
    private $relationshipToModelMap = [];

    /**
     * PrivateXeroApp constructor.
     *
     * @param $config
     *
     * @throws Exception
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->populateRelationshipToModelMap('Accounting', '');
        $this->populateRelationshipToModelMap('Assets', 'assets');
        $this->populateRelationshipToModelMap('Files', 'files');
        $this->populateRelationshipToModelMap('PayrollAU', 'payrollAU');
        $this->populateRelationshipToModelMap('PayrollUS', 'payrollUS');
    }

    /**
     * Retrieve a collection of the available relationships.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableRelationships()
    {
        $relationships = array_keys($this->relationshipToModelMap);
        sort($relationships);

        return collect($relationships);
    }

    /**
     * Populate the relationship to model map, for all models within
     * a specified model subdirectory.
     *
     * @param $modelSubdirectory
     * @param $prefix
     *
     * @throws Exception
     */
    public function populateRelationshipToModelMap($modelSubdirectory, $prefix)
    {
        $directory = Utils::getProjectRootDirectory();

        $modelsDirectory = $directory.'/vendor/calcinai/xero-php/src/XeroPHP/Models/'.$modelSubdirectory;

        $di = new RecursiveDirectoryIterator($modelsDirectory);
        foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
            if ($file->isDir() || !Str::endsWith($filename, '.php')) {
                continue;
            }

            $relationship = Str::camel($prefix.Str::plural(str_replace([$modelsDirectory, '.php', '/'], ['', '', ''], $filename)));
            $model = str_replace([$directory.'/vendor/calcinai/xero-php/src/', '/', '.php'], ['', '\\', ''], $filename);

            $this->relationshipToModelMap[$relationship] = $model;
        }
    }

    /**
     * Call a relationship method, and return a QueryWrapper.
     * Syntax: $xero->contacts().
     *
     * @param $name
     * @param $arguments
     *
     * @throws \XeroPHP\Remote\Exception
     *
     * @return QueryWrapper
     */
    public function __call($name, $arguments)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (!in_array($name, $relationships)) {
            throw new BadMethodCallException();
        }

        $model = $this->relationshipToModelMap[$name];

        return new QueryWrapper($this->load($model), $this);
    }

    /**
     * Call a relationship method and get results.
     * Syntax: $xero->contacts.
     *
     * @param $name
     *
     * @return null
     */
    public function __get($name)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (!in_array($name, $relationships)) {
            return;
        }

        return $this->$name()->get();
    }
}
