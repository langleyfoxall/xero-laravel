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
            $composer = self::normalizePath($directory.'/composer.json');
            $vendor = self::normalizePath($directory.'/vendor/');

            if (file_exists($composer) && is_dir($vendor)) {
                $root = $directory;
            }
        } while (is_null($root) && $directory != DIRECTORY_SEPARATOR);

        if (!is_null($root)) {
            return $root;
        }

        throw new Exception('Unable to determine project root directory.');
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getVendorDirectory()
    {
        return self::normalizePath(
            self::getProjectRootDirectory().'/vendor'
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function normalizePath(string $path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
