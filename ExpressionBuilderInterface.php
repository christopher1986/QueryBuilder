<?php

namespace Spark\Db;

/**
 * An ExpressionBuilder allows the construction of SQL expressions through an object oriented interface.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 * @since 1.1.0
 */
interface ExpressionBuilderInterface
{
    /**
     * Creates a logical 'AND' relation between one or more restrictions for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->andX('u.name = :name', 'u.age = :age'));
     * </code>
     * 
     * @param string|array $expressions the restrictions for this logical relation.
     * @return Composite a collection of conditions for this logical relation.
     */
    public function andX($expressions);
    
    /**
     * Creates a logical 'OR' relation between one or more restrictions for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->orX('u.name = :name', 'u.age = :age'));
     * </code>
     * 
     * @param string|array $expressions the restrictions for this logical relation
     * @return Composite a collection of conditions for this logical relation.
     */
    public function orX($expressions);
    
    /**
     * Creates an equals comparison for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->eq('u.gender', ':gender'));
     * </code>
     * 
     * @param string $name the name of the column.
     * @param mixed $value the value o named parameter.
     * @return string the SQL expression.
     */
    public function eq($name, $value);
    
    /**
     * Creates an not equals comparison for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->neq('u.gender', ':gender'));
     * </code>
     * 
     * @param string $name the name of the column.
     * @param mixed $value the value or named parameter.
     * @return string the SQL expression.
     */
    public function neq($name, $value);
    
    /**
     * Creates a greater than comparison for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->neq('u.age', ':age'));
     * </code>
     * 
     * @param string $name the name of a column.
     * @param mixed $value the value or named parameter.
     * @return string the SQL expression.
     */
    public function gt($name, $value);
    
    /**
     * Creates a greater than or equals comparison for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->gte('u.age', ':age'));
     * </code>
     * 
     * @param string $name the name of a column.
     * @param mixed $value the value or named parameter.
     * @return string the SQL expression.
     */
    public function gte($name, $value);
    
    /**
     * Creates a smaller than comparison for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->lt('u.age', ':age'));
     * </code>
     * 
     * @param string $name the name of a column.
     * @param mixed $value the value or named parameter.
     * @return string the SQL expression.
     */
    public function lt($name, $value);
    
    /**
     * Creates a smaller than or equals comparison for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->lte('u.age', ':age'));
     * </code>
     * 
     * @param string $name the name of a column.
     * @param mixed $value the value or named parameter.
     * @return string the SQL expression.
     */
    public function lte($name, $value);
    
    /**
     * Creates an AVG() function for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->avg('u.height'));
     * </code>
     * 
     * @param string|array $values the name of a column or collection of values.
     * @return string the SQL expression.
     */
    public function avg($values);
    
    /**
     * Creates a SUM() function for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->sum('u.salary'));
     * </code>
     * 
     * @param string|array $values the name of a column or collection of values.
     * @return string the SQL expression.
     */
    public function sum($values);
    
    /**
     * Creates a MAX() function for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->max('u.age'));
     * </code>
     * 
     * @param string|array $values the name of a column or collection of values.
     * @return string the SQL expression.
     */
    public function max($values);
    
    /**
     * Creates a MIN() function for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->max('u.weight'));
     * </code>
     * 
     * @param string|array $values the name of a column or collection of values.
     * @return string the SQL expression.
     */
    public function min($values);
    
    /**
     * Creates an IN clause for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->in('u.age', array(10, 20, :age, 40, 50)));
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->in('u.age', function($qb) {
     *                      return $qb->select('e.age')
     *                                ->from('employees', 'e')
     *                                ->where('e.gender', 'M');
     *                   }));
     * </code>
     * 
     * @param string $name the name of a column.
     * @param array|callable $value a collection of values or a callback function.
     * @return string the SQL expression.
     */
    public function in($name, $values);
    
    /**
     * Creates an NOT IN clause for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->notIn('u.age', array(10, 20, :age, 40, 50)));
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->notIn('u.name', function($qb) {
     *                      return $qb->select('e.name')
     *                                ->from('employees', 'e')
     *                                ->where('e.name', 'john');
     *                   }));
     * </code>
     * 
     * @param string $name the name of a column.
     * @param array|callable $value a collection of values or a callback function.
     * @return string the SQL expression.
     */
    public function notIn($name, $values);
    
    /**
     * Creates an IN clause for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->in('u.age', array(10, 20, :age, 40, 50)));
     * </code>
     * 
     * @param mixed $name the name of a column or value.
     * @param mixed $lower the lower bound value.
     * @param mixed $upper the upper bound value.
     * @return string the SQL expression.
     */
    public function between($value, $lower, $upper);
    
    /**
     * Creates a COUNT() function for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name', $qb->expr()->count('*'))
     *                 ->from('users', 'u');
     * </code>
     * 
     * @param string $name the name of a column.
     * @return string the SQL expression.
     */
    public function count($name);
    
    /**
     * Creates a IS NULL condition for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->isNull('u.email'));
     * </code>
     * 
     * @param string $name the name of a column.
     * @return string the SQL expression.
     */
    public function isNull($name);
    
    /**
     * Creates a IS NOT NULL condition for this expression.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->isNotNull('u.gender'));
     * </code>
     * 
     * @param string $name the name of a column.
     * @return string the SQL expression.
     */
    public function isNotNull($name);
}
