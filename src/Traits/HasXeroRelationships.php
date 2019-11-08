<?php

namespace LangleyFoxall\XeroLaravel\Traits;

use BadMethodCallException;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LangleyFoxall\XeroLaravel\Utils;
use LangleyFoxall\XeroLaravel\Wrappers\QueryWrapper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait HasXeroRelationships
{
    /**
     * Map between relationship names and Xero PHP library models
     *
     * @var array
     */
    private $relationshipToModelMap = [];

    /**
     * Retrieve a collection of the available relationships.
     *
     * @return Collection
     */
    public function getAvailableRelationships()
    {
        $relationships = array_keys($this->relationshipToModelMap);

        sort($relationships);

        return collect($relationships);
    }

    /**
     * Populates all relationship to model maps.
     *
     * @throws Exception
     */
    public function populateRelationshipToModelMaps()
    {
        $this->populateRelationshipToModelMap('Accounting', '');
        $this->populateRelationshipToModelMap('Assets', 'assets');
        $this->populateRelationshipToModelMap('Files', 'files');
        $this->populateRelationshipToModelMap('PayrollAU', 'payrollAU');
        $this->populateRelationshipToModelMap('PayrollUS', 'payrollUS');
    }

    /**
     * Populate the relationship to model map, for all models within
     * a specified model subdirectory.
     *
     * @param $modelSubdirectory
     * @param $prefix
     * @throws Exception
     */
    public function populateRelationshipToModelMap($modelSubdirectory, $prefix)
    {
        $vendor = Utils::getVendorDirectory();

        $dependencyDirectory = Utils::normalizePath(
            $vendor.'/calcinai/xero-php/src/'
        );

        $modelsDirectory = Utils::normalizePath(
            $dependencyDirectory.'/XeroPHP/Models/'.$modelSubdirectory
        );

        $di = new RecursiveDirectoryIterator($modelsDirectory);
        foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
            if ($file->isDir() || !Str::endsWith($filename, '.php')) {
                continue;
            }

            $relationship = Str::camel(
                $prefix.Str::plural(
                    str_replace(
                        [$modelsDirectory, '.php', DIRECTORY_SEPARATOR], '',
                        $filename
                    )
                )
            );

            $model = str_replace(
                [$dependencyDirectory, DIRECTORY_SEPARATOR, '.php'], ['', '\\'],
                $filename
            );

            $this->relationshipToModelMap[$relationship] = $model;
        }
    }

    /**
     * Call a relationship method, and return a QueryWrapper.
     * Syntax: $xero->contacts()
     *
     * @param $name
     * @param $arguments
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
