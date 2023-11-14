<?php

namespace LangleyFoxall\XeroLaravel\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LangleyFoxall\XeroLaravel\Wrappers\QueryWrapper;

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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function populateRelationshipToModelMap($modelSubdirectory, $prefix)
    {
        $vendor = $this->getVendorDirectory();

        $dependencyDirectory = $this->normalizePath(
            $vendor.'/calcinai/xero-php/src'
        );

        $modelsDirectory = $this->normalizePath(
            $dependencyDirectory.'/XeroPHP/Models/'.$modelSubdirectory
        );

        $di = new \RecursiveDirectoryIterator($modelsDirectory);
        foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
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
     * Finds and returns the project's root directory
     * (containing the composer.json file).
     *
     * @return null|string
     * @throws \Exception
     */
    private function getProjectRootDirectory()
    {
        $root = str(File::dirname(__FILE__))->before('vendor');

        if (File::exists($root . 'composer.json') && File::isDirectory($root . 'vendor')) {
            return $root;
        }

        throw new \Exception('Unable to determine project root directory.');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getVendorDirectory()
    {
        return $this->normalizePath(
            $this->getProjectRootDirectory() . 'vendor'
        );
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
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
            throw new \BadMethodCallException();
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
