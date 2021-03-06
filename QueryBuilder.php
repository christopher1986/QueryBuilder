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

namespace Spark\Db;

use Spark\Db\Adapter\AdapterAwareInterface;
use Spark\Db\Adapter\AdapterCapableInterface;
use Spark\Db\Adapter\AdapterInterface;
use Spark\Db\Query\Delete;
use Spark\Db\Query\Insert;
use Spark\Db\Query\Select;
use Spark\Db\Query\Update;

/**
 * The QueryBuilder is a concrete implementation of the {@link QueryBuilderInterface}.
 *
 * @author Chris Harris
 * @version 0.0.2
 * @since 0.0.2
 */
class QueryBuilder implements AdapterAwareInterface, AdapterCapableInterface, QueryBuilderInterface
{
    /**
     * The database adapter.
     *
     * @var AdapterInterface
     */
    private $adapter = null;

    /**
     * The expression builder.
     *
     * @var ExpressionBuilderInterface
     */
    private $builder = null;

    /**
     * Create a new QueryBuilder.
     *
     * @param AdapterInterface $adapter a database adapter.
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setDbAdapter($adapter);
    }

    /**
     * Create a Select statement for the given columns.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->select('u.name')
     *             ->from('users', 'u')
     *             ->where('u.is_active = :active')
     *             ->andWhere('u.name = :name');
     *
     *    $stmt = $qb->prepare();
     *    $stmt->bindParam(':active', 1, StatementInterface::PARAM_INT);
     *    $stmt->bindParam(':name', 'John', StatementInterface::PARAM_STR);
     *    
     *    $results = $stmt->fetchAll();
     * </code>
     *
     * @param string|array|Traversable $select either a string for a single column or a collection for multiple columns.
     * @return Select a Select object to retrieve records from the underlying database.
     * @see Select
     * @see StatementInterface
     */
    public function select($select)
    {
        $selects = (is_array($select)) ? $select : func_get_args();

        $stmt = new Select($this->adapter);
        $stmt->select($selects);
        
        return $stmt;
    }
    
    /**
     * Create a Select statement using a (raw) vendor-specific expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->rawSelect('CASE WHEN u.id IN (1, 3, 5, 7) THEN t.date_created ELSE t.last_modified END AS date');
     *
     *    $stmt   = $qb->prepare();
     *    $column = $stmt->fetchColumn();
     * </code>
     *
     * @param string $expression a raw expression.
     * @return Select a Select object to retrieve records from the underlying database.
     * @see Select
     * @see StatementInterface
     */
    public function rawselect($select)
    {
        $stmt = new Select($this->adapter);
        $stmt->rawSelect($select);
        
        return $stmt;
    }
    
    /**
     * Create an Insert statement for the given table name.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *             ->insert('users')
     *             ->values(array('name' => ':name', 'username' => ':username'));
     *
     *    $stmt = $qb->prepare();
     *    $stmt->execute(array(':name' => 'John', ':username' => 'john'));
     * </code>
     *
     * @param string $table the name of a table into which values will be inserted.
     * @return Insert an Insert object to insert records into the underlying database.
     * @see Insert
     * @see StatementInterface
     */
    public function insert($table, $alias = '')
    {
        $stmt = new Insert($this->adapter);
        $stmt->into($table, $alias);
        
        return $stmt;
    }
   
    /**
     * Create a Delete statement for the given table name.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->delete('users', 'u')
     *             ->where('u.name = :name');
     *
     *    $stmt = $qb->prepare();
     *    $stmt->execute(array(':name' => 'john'));
     * </code>
     *
     * @param string $table the name of a table whose records will be deleted.
     * @param string $alias (optional) the alias for this table.
     * @return Delete a Delete object to delete records from the underlying database.
     * @see Delete
     * @see StatementInterface
     */
    public function delete($table, $alias = '')
    {
        $stmt = new Delete($this->adapter);
        $stmt->from($table, $alias);
        
        return $stmt;
    }
    
    /**
     * Create an Update statement for the given table name.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->set('name', ':name')
     *             ->where('u.is_active = :active');
     *
     *    $stmt = $qb->prepare();
     *    $stmt->bindParam(':name', 'John', StatementInterface::PARAM_STR);
     *    $stmt->bindParam(':active', 1, StatementInterface::PARAM_INT);
     *    $stmt->execute();
     * </code>
     *
     * @param string $table the name of a table whose records will be updated.
     * @param string $alias (optional) the alias for this table.
     * @return Update an Update object to update records from the underlying database.
     * @see Update
     * @see ConnectionInterface
     */  
    public function update($table, $alias = '')
    {
        $stmt = new Update($this->adapter);
        $stmt->from($table, $alias);
        
        return $stmt; 
    }
    
    /**
     * {@inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder()
     *             ->update('users', 'u')
     *             ->set('name', ':name')
     *             ->where('u.is_active = :active');
     *
     *    $stmt = $qb->prepare();
     *    $stmt->bindParam(':name', 'John', StatementInterface::PARAM_STR);
     *    $stmt->bindParam(':active', 1, StatementInterface::PARAM_INT);
     *    $stmt->execute();
     * </code>
     */
    public function expr()
    {
        if ($this->builder === null) {
            $this->builder = new ExpressionBuilder($this);
        }
        
        return $this->builder;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDbAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDbAdapter()
    {
        return $this->adapter;
    }
}
