<?php
/**
 * User: Parvez
 * Date: 11/21/2017
 * Time: 6:12 AM
 */

namespace Stormifier\Database;


use ArrayIterator;
use Traversable;

class ModelCollection implements \JsonSerializable, \IteratorAggregate
{
    /**
     * @var ArrayIterator
     */
    protected $models;

    /**
     * ModelCollection constructor.
     * @param $models
     */
    public function __construct($models = [])
    {
        $this->models = new ArrayIterator($models);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->models->count();
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Iterator</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->models;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->models->getArrayCopy();
    }
}