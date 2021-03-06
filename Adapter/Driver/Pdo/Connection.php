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

use PDO;

use Spark\Db\QueryBuilder;
use Spark\Db\Adapter\Driver\ConnectionInterface;

/**
 *
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
class Connection implements ConnectionInterface
{
    /**
     * A resource to connect with a database.
     *
     * @var PDO
     */
    private $pdo;
    
    /**
     * Create a new connection.
     *
     * @param PDO $pdo the resource to connect with the database.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare($query, array $params = array())
    {
        $stmt   = new Statement($this->pdo->prepare($query));
        $params = array_filter($params, 'is_array');
        foreach ($params as $param) {
            $type = (isset($param['type'])) ? $param['type'] : PDO::PARAM_STR;
            if (isset($param['name'], $param['value'])) {
                $stmt->bindParam($param['name'], $param['value'], $type);
            }
        }
        
        return $stmt;
    }
    
    /**
     * {@inheritDoc}
     */
    public function query($query)
    {
        $stmt = new Statement($this->pdo->prepare($query));
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        return $this->pdo;
    }
    
    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }
}
