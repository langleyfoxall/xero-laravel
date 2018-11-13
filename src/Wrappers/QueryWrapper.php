<?php

namespace LangleyFoxall\XeroLaravel\Wrappers;

use Illuminate\Support\Collection;
use LangleyFoxall\XeroLaravel\Apps\PrivateXeroApp;
use XeroPHP\Remote\Query;

/**
 * Class QueryWrapper.
 */
class QueryWrapper
{
    /**
     * The original Xero PHP Query object.
     *
     * @var Query
     */
    private $query;

    /**
     * The Xero app object.
     *
     * @var Query
     */
    private $app;

    /**
     * Builds a QueryWrapper around a Query object.
     *
     * @param Query          $query
     * @param PrivateXeroApp $app
     */
    public function __construct(Query $query, PrivateXeroApp $app)
    {
        $this->query = $query;
        $this->app = $app;
    }

    /**
     * Pass through any undefined methods to the wrapped Query object.
     * If the response is another Query object, asssign it to the wrapper
     * and return it, otherwise, just return it.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $returnValue = call_user_func_array([$this->query, $name], $arguments);

        if (is_object($returnValue) && get_class($returnValue) === Query::class) {
            $this->query = $returnValue;

            return $this;
        }

        return $returnValue;
    }

    /**
     * Runs the query's execute method and returns the result
     * as a Laravel collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return collect($this->query->execute());
    }

    /**
     * Returns the first element of the retrieved collection.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->get()->first();
    }

    /**
     * Retrieves a Xero model if passed a single GUID.
     * Retrieves a collection of Xero models if passed an array or collection of GUIDs.
     *
     * @param string|array|Collection $guid
     *
     * @throws \XeroPHP\Exception
     * @throws \XeroPHP\Remote\Exception\NotFoundException
     *
     * @return null|\XeroPHP\Remote\Collection|\XeroPHP\Remote\Model
     */
    public function find($guid)
    {
        if (is_object($guid) && get_class($guid) === Collection::class) {
            $guid = $guid->toArray();
        }

        if (is_array($guid)) {
            return collect($this->app->loadByGUIDs($this->getClass(), implode(',', $guid)));
        }

        return $this->app->loadByGUID($this->getClass(), $guid);
    }

    /**
     * Get the Xero class (Contact, Invoice, etc.) that the
     * wrapped query object is using.
     *
     * @return string
     */
    private function getClass()
    {
        return $this->query->getFrom();
    }
}
