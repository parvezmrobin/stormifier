<?php
/**
 * User: Parvez
 * Date: 11/21/2017
 * Time: 3:42 AM
 */

namespace Stormifier\Database;


use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;
use Stormifier\Assistant\Config;

class Query
{
    protected $dbType;
    protected $host;
    protected $dbName;
    protected $username;
    protected $password;
    protected $isDev;

    protected $pdo;

    /**
     * Query constructor.
     */
    public function __construct()
    {
        $config = Config::from('env');

        $this->dbType = $config->get('db');
        $credentials = $config->get($this->dbType);
        $this->host = $credentials['host'];
        $this->dbName = $credentials['name'];
        $this->username = $credentials['user'];
        $this->password = $credentials['password'];
        $this->isDev = $config->get('dev');

        try {
            $this->pdo = new PDO(
                "$this->dbType:host=$this->host;dbname=$this->dbName",
                $this->username,
                $this->password
            );
        } catch (PDOException $e) {
            throw new InvalidArgumentException("Invalid DB connection parameters");
        }
    }

    /**
     * @return static
     */
    public static function build()
    {
        return new static();
    }

    /**
     * @param string $query
     * @return array|bool
     */
    public function query(string $query)
    {
        $result = $this->pdo->query($query);
        return (is_bool($result))? $result: $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @return int|bool
     */
    public function execute(string $sql)
    {
        return $this->pdo->exec($sql);
    }

    /**
     * @param string $sql
     * @return string
     */
    public function executeGetId(string $sql)
    {
        $this->pdo->exec($sql);
        return $this->pdo->lastInsertId();
    }

    /**
     * Executes a database transaction
     * @param string[] $sqls Array containing the $sql statements
     * @return bool
     * @throws Exception
     */
    public function transact($sqls)
    {
        $sqls = (array) $sqls;

        try {
            $this->pdo->beginTransaction();
            foreach ($sqls as $sql) {
                if (!$this->pdo->exec($sql)) {
                    throw new PDOException("Error in SQL: $sql");
                };
            }
            return $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();

            if ($this->isDev) {
                throw $e;
            }
        }

        return false;
    }

    /**
     * Alias of transact
     * @param $sqls
     * @return bool
     */
    public function executeMultiple($sqls)
    {
        return $this->transact($sqls);
    }

    public function createStatement(string $statement)
    {
        return $this->pdo->prepare($statement);
    }
}