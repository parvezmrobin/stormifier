<?php
/**
 * User: Parvez
 * Date: 11/21/2017
 * Time: 3:41 AM
 */

namespace Stormifier\Database;


use ArrayAccess;
use Doctrine\Common\Inflector\Inflector;
use IteratorAggregate;
use JsonSerializable;
use Symfony\Component\HttpFoundation\ParameterBag;
use Traversable;

class Model implements ArrayAccess, JsonSerializable, IteratorAggregate
{
    protected $primaryKey;

    protected $keyType;

    protected $autoIncrement;

    protected $table;

    protected $timestamp = true;

    protected $dateFormat;

    protected $casts = [];

    /**
     * @var ParameterBag
     */
    protected $data;

    protected $relations = [];

    // TODO: Hidden Support

    // TODO: Fillable Support

    protected $query;

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->query = new Query();
        $this->data = new ParameterBag($data);
    }

    public static function all()
    {
        $table = (new \ReflectionClass(static::class))->getShortName();
        $table = Inflector::pluralize($table);
        $table = Inflector::tableize($table);

        $result = Query::build()->query("SELECT * FROM $table");

        return $result;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        // TODO: Cast Support
        // TODO: Relation Support
        // TODO: Time Stamp Support
        // TODO: Accessor Support
        return $this->data->get($key);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function __set(string $key, string $value)
    {
        // TODO: Mutator Support

        $this->data->set($key, $value);
    }

    public function __toString()
    {
        // TODO: Hidden Support
        return json_encode($this->data->all());
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->data->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->data->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->data->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->data->remove($offset);
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
        return $this->data->all();
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
        return $this->data->getIterator();
    }
}