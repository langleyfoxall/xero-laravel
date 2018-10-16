<?php
namespace LangleyFoxall\XeroLaravel;

use XeroPHP\Remote\Query;

/**
 * Class QueryWrapper
 * @package LangleyFoxall\XeroLaravel
 */
class QueryWrapper
{
    /**
     * The original Xero PHP Query object
     *
     * @var Query
     */
    private $query;

    /**
     * Builds a QueryWrapper around a Query object
     *
     * QueryWrapper constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Pass through any undefined methods to the wrapped Query object.
     * If the response is another Query object, asssign it to the wrapper
     * and return it, otherwise, just return it.
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $returnValue = call_user_func_array([$this->query, $name], $arguments);

        if (is_object($returnValue) && get_class($returnValue)==Query::class) {
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

}