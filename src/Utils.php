<?php

namespace LangleyFoxall\XeroLaravel;

use Exception;

abstract class Utils
{
    /**
     * Finds and returns the project's root directory
     * (containing the composer.json file).
     *
     * @return null|string
     * @throws Exception
     */
    public static function getProjectRootDirectory()
    {
        $root = null;
        $directory = dirname(__FILE__);

        do {
            $directory = dirname($directory);
            $composer = $directory . '/composer.json';
            if(file_exists($composer)) {
                $root = $directory;
            }
        } while(is_null($root) && $directory != '/');

        if(!is_null($root)) {
            return $root;
        }

        throw new Exception('Unable to locate `composer.json`.');
    }
}