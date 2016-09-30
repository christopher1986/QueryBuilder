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

namespace Spark\Db\Adapter\Driver\Pdo;

use ArrayIterator;
use IteratorAggregate;
use PDO;
use PDOStatement;

use Spark\Db\Adapter\Driver\StatementInterface;

/**
 *
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
class Statement implements StatementInterface, IteratorAggregate
{    
    /**
     * The underlying PDO statement.
     *
     * @var PDOStatement
     */
    private $statement = null;

    /**
     * A collection of parameter objects.
     *
     * @var array
     */
    private $params = array();

    /**
     * Create a new connection.
     *
     * @param string $query the underlying statement created by PDO.
     */
    public function __construct(PDOStatement $statement)
    {
        $this->pdoStmt = $statement;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $params = array())
    {
        $stmt = $this->pdoStmt;
        return (!empty($params)) ? $stmt->execute($params) : $stmt->execute();
    }
    
    /**
     * {@inheritDoc}
     */
    public function fetch($type = self::FETCH_OBJ, $rowOffset = 0)
    {
        $style = $this->getFetchStyle($type);
        return $this->pdoStmt->fetch($style, PDO::FETCH_ORI_NEXT, $rowOffset);
    }
    
    /**
     * {@inheritDoc}
     */
    public function fetchAll($type = self::FETCH_OBJ)
    {
        $style = $this->getFetchStyle($type);
        return $this->pdoStmt->fetchAll($style);
    }
    
    /**
     * {@inheritDoc}
     */
    public function fetchColumn($columnOffset = 0)
    {        
        return $this->pdoStmt->fetchColumn($columnOffset);
    }
    
    /**
     * {@inheritDoc}
     */
    public function rowCount()
    {        
        return $this->pdoStmt->rowCount();
    }
    
    /**
     * {@inheritDoc}
     */
    public function bindParam($name, $value, $type = PDO::PARAM_STR)
    {
        if ($this->pdoStmt->bindParam($name, $value, $type)) {
            $this->params[$name] = true;
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function unbindParam($name)
    {
        if ($this->hasParam($name)) {
            $this->pdoStmt->bindParam($param, '', PDO::PARAM_STR);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasParam($name)
    {
        return (isset($this->params[$name]));
    }
    
    /**
     * {@inheritDoc}
     */
    public function clearParams()
    {
        foreach ($this->params as $name) {
            $this->pdoStmt->bindParam($name, '', PDO::PARAM_STR);
        }
    }
    
    /**
     * Returns an external iterator over the resultset of this statement in proper sequence. 
     *
     * @return ArrayIterator an iterator over the resultset of this statement.
     */
    public function getIterator()
    {
        $data = $this->fetchAll();
        return new ArrayIterator($data);
    }
    
    /**
     * Returns the appropriate PDO fetch style for the specified type.
     *
     * @param string $type the {@link Statement} fetch style.
     * @return mixed the equivelant PDO fetch style.
     */
    private function getFetchStyle($type) 
    {
        switch ($type) {
            case self::FETCH_ASSOC:
                $type = PDO::FETCH_ASSOC;
                break;
            case self::FETCH_NUM:
                $type = PDO::FETCH_NUM;
                break;
            case self::FETCH_OBJ:
                $type = PDO::FETCH_OBJ;
                break;
        }
        
        return $type;
    }
}
