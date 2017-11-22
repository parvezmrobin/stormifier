<?php
/**
 * User: Parvez
 * Date: 11/21/2017
 * Time: 6:32 AM
 */

namespace Stormifier\Database;

use PDO;

class QueryBuilder
{
    protected $selects   = [];
    protected $wheres    = [];
    protected $orWheres  = [];
    protected $table;
    protected $joins     = [];
    protected $bindings  = [];
    protected $orderBys  = [];
    protected $groupBys  = [];
    protected $havings   = [];
    protected $orHavings = [];

    function __construct($table)
    {
        $this->table = $table;
    }

    public static function table($table)
    {
        return new static($table);
    }

    public function join($table, $column1, $column2)
    {
        $numArgs = func_num_args();

        if ($numArgs === 3) {
            $this->addToJoins($table, $column1, '=', $column2, 'inner');
        } elseif ($numArgs === 4) {
            $this->addToJoins($table, $column1, $column2, func_get_arg(3), 'inner');
        } elseif ($numArgs !== 5) {
            $this->addToJoins($table, $column1, $column2, func_get_arg(3), func_get_arg(4));
        }

        return $this;
    }

    protected function addToJoins($table, $column1, $operator, $column2, $type)
    {
        $this->joins[] = func_get_args();
    }

    public function leftJoin($table, $column1, $column2)
    {
        $numArgs = func_num_args();

        if ($numArgs === 3) {
            $this->addToJoins($table, $column1, '=', $column2, 'left');
        } elseif ($numArgs === 4) {
            $this->addToJoins($table, $column1, $column2, func_get_arg(3), 'left');
        } else {
            throw new \InvalidArgumentException("Invalid number of argument");
        }

        return $this;
    }

    public function rightJoin($table, $column1, $column2)
    {
        $numArgs = func_num_args();

        if ($numArgs === 3) {
            $this->addToJoins($table, $column1, '=', $column2, 'right');
        } elseif ($numArgs === 4) {
            $this->addToJoins($table, $column1, $column2, func_get_arg(3), 'right');
        } else {
            throw new \InvalidArgumentException("Invalid number of argument");
        }

        return $this;
    }

    public function where($column)
    {
        if (func_num_args() === 1) {
            $wheres = [];

            foreach ($column as $item) {
                $wheres[] = $this->parseWhere($item);
            }

            $this->wheres[] = $wheres;
            return $this;
        }

        $this->wheres[] = $this->parseWhere(func_get_args());

        return $this;
    }

    protected function parseWhere(array $params)
    {
        $numParam = count($params);

        if ($numParam === 2) {
            $params[] = $params[1];
            $params[1] = '=';
            $params[] = true;
        } elseif ($numParam === 3) {
            $params[] = true;
        } elseif ($numParam === 4) {
            if (is_string($params[3])) {
                $params[3] = !strcasecmp("and", $params[3]);
            } elseif (!is_bool($params[3])) {
                throw new \InvalidArgumentException("Invalid number of argument");
            }
        }
        return $params;
    }

    public function orWhere($column)
    {
        if (func_num_args() === 1) {
            $wheres = [];

            foreach ($column as $item) {
                $wheres[] = $this->parseWhere($item);
            }

            $this->orWheres[] = $wheres;
            return $this;
        }

        $this->orWheres[] = $this->parseWhere(func_get_args());

        return $this;
    }

    public function select($columns)
    {
        if (func_num_args() > 1) {
            $columns = func_get_args();
        } else {
            $columns = (array)$columns;
        }
        $this->selects = array_merge($this->selects, $columns);

        return $this;
    }

    public function orderBy($columns)
    {
        if (func_num_args() > 1) {
            $columns = func_get_args();
        } else {
            $columns = (array)$columns;
        }

        $this->orderBys = array_merge($this->orderBys, $columns);

        return $this;
    }

    public function groupBy($columns)
    {
        if (func_num_args() > 1) {
            $columns = func_get_args();
        } else {
            $columns = (array)$columns;
        }

        $this->groupBys = array_merge($this->groupBys, $columns);

        return $this;
    }

    public function having($column)
    {
        if (func_num_args() === 1) {
            $havings = [];

            foreach ($column as $item) {
                $havings[] = $this->parseWhere($item);
            }

            $this->havings[] = $havings;
            return $this;
        }

        $this->havings[] = $this->parseWhere(func_get_args());

        return $this;
    }

    public function orHaving($column)
    {
        if (func_num_args() === 1) {
            $havings = [];

            foreach ($column as $item) {
                $havings[] = $this->parseWhere($item);
            }

            $this->orHavings[] = $havings;
            return $this;
        }

        $this->orHavings[] = $this->parseWhere(func_get_args());

        return $this;
    }

    /**
     * @return array
     */
    public function query()
    {
        $statement = $this->generateStatement();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return \PDOStatement
     */
    public function generateStatement(): \PDOStatement
    {
        $query = $this->addSelects();
        $query = $this->addJoins($query);
        $query = $this->addWheres($query);
        $query = $this->addWheres($query, 'or');
        $query = $this->addGroupBys($query);
        $query = $this->addHaving($query);
        $query = $this->addHaving($query, 'or');
        $query = $this->addOrderBys($query);

        $numBinds = count($this->bindings);
        $statement = Query::build()->createStatement($query);

        for ($i = 0; $i < $numBinds; $i++) {
            $statement->bindParam($i + 1, $this->bindings[$i]);
        }
        return $statement;
    }

    /**
     * @return string
     */
    protected function addSelects(): string
    {
        $query = "SELECT " . implode(", ", $this->selects) . " FROM " . $this->table;
        return $query;
    }

    /**
     * @param $query
     * @return string
     */
    protected function addJoins($query): string
    {
        foreach ($this->joins as $join) {
            $query .= (" " . strtoupper($join[4]) . " JOIN $join[0] ON $join[1] $join[2] $join[3]");
        }
        return $query;
    }

    /**
     * @param $query
     * @param string $type
     * @return string
     * @internal param $conditions
     */
    protected function addWheres($query, $type = 'and')
    {
        $conditions = (!strcasecmp("and", $type)) ? $this->wheres : $this->orWheres;
        $query = $this->addConditionalClause($query, $conditions, $type, "where");

        return $query;
    }

    /**
     * @param $query
     * @param $conditions
     * @param $andOr
     * @param $type
     * @return string
     */
    protected function addConditionalClause($query, $conditions, $andOr, $type): string
    {
        $isAnd = strcasecmp($andOr, "or");
        $len = count($conditions);

        for ($i = 0; $i < $len; $i++) {
            $condition = $conditions[$i];
            // If not first where clause
            if ($i === 0 && $isAnd) {
                $query .= (" " . strtoupper($type) . " ");
            } else {
                $query .= (" " . strtoupper($andOr . " " . $type) . " ");
            }

            $query = $this->appendClause($query, $condition);
        }
        return $query;
    }

    /**
     * @param $query
     * @param $condition
     * @return string
     * @internal param $i
     */
    protected function appendClause(string $query, array $condition): string
    {
        // If nested where
        if (is_array($condition[0])) {
            $query .= "(";

            $len = count($condition);
            for ($j = 0; $j < $len; $j++) {
                $nestedCondition = $condition[$j];
                // If not first condition clause
                // then use AND or OR
                if ($j > 0) {
                    if ($nestedCondition[3]) {
                        $query .= " AND ";
                    } else {
                        $query .= " OR ";
                    }
                }

                $query = $this->appendCondition($nestedCondition, $query);
            }

            $query .= ")";
            return $query;
        } else {
            $query = $this->appendCondition($condition, $query);
            return $query;
        }
    }

    /**
     * @param $nestedWhere
     * @param $query
     * @return string
     */
    protected function appendCondition($nestedWhere, $query): string
    {
        $query .= ("$nestedWhere[0] $nestedWhere[1] ?");
        $this->bindings[] = $nestedWhere[2];
        return $query;
    }

    protected function addGroupBys($query)
    {
        $query .= (" GROUP BY " . implode(", ", $this->groupBys));

        return $query;
    }

    /**
     * @param $query
     * @param string $type
     * @return string
     * @internal param $conditions
     */
    protected function addHaving($query, $type = 'and')
    {
        $conditions = (!strcasecmp("and", $type)) ? $this->havings : $this->orHavings;
        $query = $this->addConditionalClause($query, $conditions, $type, "having");

        return $query;
    }

    protected function addOrderBys($query)
    {
        return $query . " ORDER BY " . implode(", ", $this->orderBys);
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->generateStatement()->queryString;
    }
}