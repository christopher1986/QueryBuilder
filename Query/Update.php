<?php
/**
 * Copyright (c) 2015, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2015 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Db\Query;

use Spark\Db\Sql\Expression\Composite as CompositeExpression;

use Spark\Db\Sql\Alias;
use Spark\Db\Sql\From;
use Spark\Db\Sql\Limit;
use Spark\Db\Sql\Order;

/**
 *
 *
 * @author Chris Harris
 * @version 0.0.1
 * @since 0.0.2
 */
class Update extends AbstractSql implements FilterCapableInterface, LimitCapableInterface, OrderCapableInterface
{
    /**
     * The parts that form the select statement.
     *
     * @var array
     */
    protected $parts = array(
        'table'   => null,
        'set'     => array(),
        'where'   => null,
        'orderBy' => array(),
        'limit'   => null,
    );
    
    /**
     * Specify from which table to retrieve rows.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u');
     * </code>
     *
     * @param string $from the table name.
     * @param string $alias (optional) the alias for this table.
     * @throws InvalidArgumentException if the first argument is not a 'string' type.
     */
    public function from($from, $alias = '')
    {
        if (!is_string($from)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($from)) ? get_class($from) : gettype($from)
            ));
        }
    
        if (is_string($alias) && $alias !== '') {
            $from = new Alias($from, $alias);
        }
        
        $this->addQueryPart('from', $from, false);
        return $this;
    }
    
    /**
     * Add a column and the value to the statement.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->set('name', ':name');
     * </code>
     *
     * @param string $column the name of a column.
     * @param mixed $value the value.
     * @return Update allows a fluent interface to be created.
     */
    public function set($column, $value)
    {
        $this->parts['set'][$column] = sprintf('%s = %s', $column, $value);
        return $this;
    }

    /**
     * Add one or more restrictions to this statement and create a 
     * logical 'AND' relation with any previous restrictions. Replaces any
     * previously restrictions that were set.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->set('name', :name)
     *             ->where('u.is_active = :active');
     * </code>
     *
     * @param string|array $where one or more restrictions.
     */
    public function where($where)
    {        
        $this->clearQueryPart('where');        
        return $this->andWhere(func_get_args());
    }

    /**
     * Add one or more restrictions to this statement and create a 
     * logical 'AND' relation with any previous restrictions.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->set('name', :name)
     *             ->where('u.is_active = :active')
     *             ->andWhere('u.date_created = :date');
     * </code>
     *
     * @param string|array $where one or more restrictions.
     */
    public function andWhere($where)
    {
        $clauses = (is_array($where)) ? $where : func_get_args();        
        $this->createWhere($clauses, CompositeExpression::TYPE_AND);
        
        return $this;
    }
    
    /**
     * Add one or more restrictions to this statement and create a 
     * logical 'OR' relation with any previous restrictions.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->set('name', :name)
     *             ->where('u.is_active = :active')
     *             ->orWhere('u.date_created = :date');
     * </code>
     *
     * @param string|array $where one or more restrictions.
     */
    public function orWhere($where)
    {
        $clauses = (is_array($where)) ? $where : func_get_args();
        $this->createWhere($clauses, CompositeExpression::TYPE_OR);
        
        return $this;
    }
    
    /**
     * Specifies how the results should be ordered. Removes if set any previously ordering.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->orderBy('u.date_created', 'ASC');
     * </code>
     *
     * @param string $column the column to order by.
     * @param string $sort how to sort the results, only 'ASC' and 'DESC' are allowed.
     */
    public function orderBy($order, $sort = Order::SORT_ASC)
    {
        $this->clearQueryPart('orderBy', array());
        $this->addOrderBy($order, $sort);
        
        return $this;
    }
    
    /**
     * Specifies additional ordering to be applied on the query results.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->orderBy('u.date_created', 'ASC')
     *             ->addOrderBy('u.name', 'ASC');
     * </code>
     *
     * @param string $column the column to order by.
     * @param string $sort how to sort the results, only 'ASC' and 'DESC' are allowed.
     */
    public function addOrderBy($order, $sort = Order::SORT_ASC)
    {
        $this->addQueryPart('orderBy', new Order($order, $sort));        
        return $this; 
    }

    /**
     * Specifies the number of rows to update. Passing a 'null' literal will 
     * remove any previously set limit.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->limit(5);
     * </code>
     *
     * @param int|null $limit the number of result to return.
     */
    public function limit($limit = null)
    {
        $this->clearQueryPart('limit');
        if ($limit !== null) {
            $this->addQueryPart('limit', new Limit($limit), false);
        }
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSqlString()
    {
        if ($this->isClean()) {
            return $this->query;
        }
        
        $query = sprintf('UPDATE %s ', $this->parts['from']);
        if (!empty($this->parts['set'])) {
            $query .= sprintf('SET %s ', implode(', ', $this->parts['set']));
        }
        if (($this->parts['where'] instanceof CompositeExpression) && !$this->parts['where']->isEmpty()) {
            $query .= sprintf('WHERE %s ', $this->parts['where']);
        }
        if (!empty($this->parts['orderBy'])) {
            $query .= sprintf('ORDER BY %s ', implode(', ', $this->parts['orderBy']));
        }
        if (!empty($this->parts['limit'])) {
            $query .= $this->limitResults($this->parts['limit']);
        }
        
        // update state of object.
        $this->setState(self::IS_CLEAN);
        // store generated SQL statement.
        $this->query = rtrim($query);

        return $this->query;
    }
     
    /**
     * Creates a composite of expressions
     *
     * @param array $expressions a collection containing zero or more expressions.
     * @param int $type the relationship between the one or more where clauses.
     */
    private function createWhere(array $expressions, $type = CompositeExpression::TYPE_AND)
    {        
        $where = $this->getQueryPart('where');
        if ($where instanceof CompositeExpression) {
            if ($where->getType() === $type) {
                $where->addAll($expressions);
            } else {
                array_unshift($expressions, $where);
                $this->addQueryPart('where', new CompositeExpression($type, $expressions), false);
            }
        } else {
            $this->addQueryPart('where', new CompositeExpression($type, $expressions), false);
        }
    }
}
