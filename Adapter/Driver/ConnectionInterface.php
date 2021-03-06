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

namespace Spark\Db\Adapter\Driver;

/**
 *
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
interface ConnectionInterface
{
    /**
     * Prepares a SQL statement for execution.A prepared statement will be safe from SQL injections 
     * because all parameters will be properly escaped.
     *
     * @param string $query the SQL statement to prepare.
     * @param array $params (optional) a collection of parameters associated with the SQL statement.
     * @return StatementInterface a StatementInterface object.
     * @link https://codex.wordpress.org/Class_Reference/wpdb#Protect_Queries_Against_SQL_Injection_Attacks
     */
    public function prepare($query, array $params = array());
    
    /**
     * Executes an SQL statement. To protect against SQL injection the data inside the statement must
     * be properly escaped. As an alternative the {@link ConnectionInterface::prepare} method can be
     * used which will ensure that all parameters are properly escaped.
     *
     * @param string $query a properly escaped SQL statement.
     * @link https://codex.wordpress.org/Class_Reference/wpdb#Running_General_Queries
     */
    public function query($query);
    
    /**
     * Returns the resource that communicates with the underlying data source.
     *
     * @return mixed the object that communicates with the underlying data source.
     */
    public function getResource();
    
    /**
     * Returns the id of last inserted row.
     *
     * @return mixed the id of last insered row.
     * @link http://php.net/manual/en/pdo.lastinsertid.php
     */
    public function lastInsertId();
    
    /**
     * Returns the last error information from the database resource.
     *
     * @return string|array the last error information.
     */
     public function errorInfo();
}
