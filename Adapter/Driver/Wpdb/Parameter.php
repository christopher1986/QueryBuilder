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

namespace Spark\Db\Adapter\Driver\Wpdb;

/**
 *
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
class Parameter implements ParameterInterface
{
    /**
     * Indicates a string value.
     *
     * @var string
     */
    const PARAM_STR = 'string';
    
    /**
     * Indicates an integer value.
     *
     * @var string
     */
    const PARAM_INT = 'int';
    
    /**
     * Indicates a float value.
     *
     * @var string
     */
    const PARAM_FLOAT = 'float';

    /**
     * The parameter name.
     *
     * @var string
     */
    private $name = '';
    
    /**
     * The parameter value.
     * 
     * @var mixed
     */
    private $value = null;
    
    /**
     * The data type for the parameter value.
     *
     * @var string a valid parameter type.
     */
    private $type = self::PARAM_STR;
    
    /**
     * Create a new parameter.
     *
     * @param string $name the parameter name.
     * @param mixed $value the value to set.
     * @param string|null $type (optional) the data type for this parameter, defaults to null.
     */
    public function __construct($name, $value = '', $type = null)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setType($type);
    }
    
    /**
     * {@inheritDoc}
     */
    private function setName($name)
    {        
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
        }
    
        $this->name = $name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set data type of the value.
     *
     * @param int $type the data type.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue()
    {        
        return $this->convertValue($this->value, $this->getType());
    }
    
    /**
     * Set data type of the value.
     *
     * @param int $type the data type.
     */
    public function setType($type)
    {
        $allowed = array(
            self::PARAM_STR, 
            self::PARAM_INT, 
            self::PARAM_FLOAT,
        );
        
        $this->type = (in_array($type, $allowed)) ? $type : self::PARAM_STR;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Reset parameter to it's original state.
     */
    public function reset()
    {
        $this->value = '';
        $this->type  = self::PARAM_STR;
    }
    
    /**
     * Returns a value of the appropriate type.
     *
     * Since there no guarantee that the value can actually be cast typed into the given type it is 
     * the programmer's responsibility to ensure that the given value does match with given type, 
     * failing to do so may result in unexpected behaviour such as ending up with wrong values.
     *
     * @param mixed $value the value that will be converted.
     * @param int $type the type into which the value will be converted.
     * @return mixed the converted value.
     * @link http://stackoverflow.com/questions/833510/php-pdobindparam-data-types-how-does-it-work#answer-865979
     */
    private function convertValue($value, $type = self::PARAM_STR)
    {
        if (is_array($value)) {
            $values = $value;
            foreach ($values as $key => $value) {
                $values[$key] = $this->convertValue($value, $type);
            }
            return $values;
        }
        
        if ($type === self::PARAM_INT) {
            $value = $this->convertToInt($value);
        } else if ($type === self::PARAM_FLOAT) {
            $value = $this->convertToFloat($value);
        } else {
            $value = $this->convertToString($value);
        }
        
        return $value;
    }
    
    /**
     * Returns a string representation of the given value.
     * 
     * The given value can be any scalar value or object that implements the __toString() method.
     * Other values such as resources or objects that have not string representation will be 
     * will be ignored which will result in an empty string being returned.
     *
     * @param mixed $value the value to convert.
     * @return string the value converted to a string type.
     */
    private function convertToString($value)
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string) $value;
        }
        
        return (is_scalar($value)) ? (string) $value : '';
    }
    
    /**
     * Returns an integer value of the given value.
     * 
     * The given value can be any scalar value or object that implements the __toString() method.
     * Other values such as resources or objects that have not string representation will be 
     * ignored which will result in a zero integer value (0) being returned.
     *
     * @param mixed $value the value to convert.
     * @return int the value converted to an integer type.
     */
    private function convertToInt($value)
    {        
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string) $value;
        }
        
        return (is_scalar($value)) ? (int) $value : 0;
    }
    
    /**
     * Returns a floating point value of the given value.
     * 
     * The given value can be any scalar value or object that implements the __toString() method. 
     * Other values such as resources or objects that have not string representation will be 
     * ignored which will result in a zero floating value (0.0) being returned.
     *
     * @param mixed $value the value to convert.
     * @return float the value converted to a float type.
     */
    private function convertToFloat($value)
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string) $value;
        }
             
        return (is_scalar($value)) ? (float) $value : 0.0;
    }
}
