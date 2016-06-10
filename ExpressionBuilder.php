<?php

namespace Spark\Db;

use Spark\Db\Exception\UnexpectedResultException;
use Spark\Db\Query\Select;
use Spark\Db\Sql\Expression\Composite as CompositeExpression;

/**
 * The ExpressionBuilder is a concrete implementation of the {@link ExpressionBuilderInterface}.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 0.0.1
 * @since 1.0.0
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * The query builder.
     *
     * @var QueryBuilderInterface
     */
    private $builder = null;

    /**
     * Create a new ExpressionBuilder.
     *
     * @param QueryBuilderInterface $builder the query builder.
     */
    public function __construct(QueryBuilderInterface $builder)
    {
        $this->setQueryBuilder($builder);
    }

    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->andX('u.name = :name', 'u.age = :age'));
     * </code>
     */
    public function andX($expressions)
    {
        $expressions = (is_array($expressions)) ? $expressions : func_get_args();
        return new CompositeExpression(CompositeExpression::TYPE_AND, $expressions)
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->orX('u.name = :name', 'u.age = :age'));
     * </code>
     */
    public function orX($expressions)
    {
        $expressions = (is_array($expressions)) ? $expressions : func_get_args();
        return new CompositeExpression(CompositeExpression::TYPE_OR, $expressions)
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->eq('u.gender', ':gender'));
     * </code>
     */
    public function eq($name, $value)
    {
        return sprintf('%s = %s', $name, $value);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->neq('u.gender', ':gender'));
     * </code>
     */
    public function neq($name, $value)
    {
        return sprintf('%s <> %s', $name, $value);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->neq('u.age', ':age'));
     * </code>
     */
    public function gt($name, $value)
    {
        return sprintf('%s > %s', $name, $value);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->gte('u.age', ':age'));
     * </code>
     */
    public function gte($name, $value)
    {
        return sprintf('%s >= %s', $name, $value);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->lt('u.age', ':age'));
     * </code>
     */
    public function lt($name, $value)
    {
        return sprintf('%s < %s', $name, $value);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->lte('u.age', ':age'));
     * </code>
     */
    public function lte($name, $value)
    {
        return sprintf('%s <= %s', $name, $value);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->avg('u.height'));
     * </code>
     */
    public function avg($values)
    {
        $values = (is_array($values)) ? $values : func_get_args();
        return sprintf('AVG(%s)', implode(', ', $values));
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->sum('u.salary'));
     * </code>
     */
    public function sum($values)
    {
        $values = (is_array($values)) ? $values : func_get_args();
        return sprintf('SUM(%s)', implode(', ', $values));
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->max('u.age'));
     * </code>
     */
    public function max($values)
    {
        $values = (is_array($values)) ? $values : func_get_args();
        return sprintf('MAX(%s)', implode(', ', $values));
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->max('u.weight'));
     * </code>
     */
    public function min($values)
    {
        $values = (is_array($values)) ? $values : func_get_args();
        return sprintf('MIN(%s)', implode(', ', $values));
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->in('u.age', function($qb) {
     *                       return $qb->select('e.age')
     *                                 ->from('employees', 'e')
     *                                 ->where('e.gender', ':gender')
     *                                 ->limit(10);
     *                   }));
     * </code>
     *
     * @throws UnexpectedResultException if the second argument is a callback function but does not return a {@link Select} instance.
     */
    public function in($name, $values)
    {
        if (is_callable($values)) {
            $values = array($this->createSubQuery($values));
        } else if (!is_array($values)) {
            $values = func_get_args();
        } 
    
        return sprintf('IN (%s)', implode(', ', $values));
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->notIn('u.height', function($qb) {
     *                       return $qb->select('e.height')
     *                                 ->from('employees', 'e')
     *                                 ->where('e.height', ':height')
     *                                 ->limit(10);
     *                   }));
     * </code>
     *
     * @throws UnexpectedResultException if the second argument is a callback function but does not return a {@link Select} instance.
     */
    public function notIn($name, $values)
    {
        if (is_callable($values)) {
            $values = array($this->createSubQuery($values));
        } else if (!is_array($values)) {
            $values = func_get_args();
        } 
    
        return sprintf('NOT IN (%s)', implode(', ', $values));
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->in('u.age', array(10, 20, :age, 40, 50)));
     * </code>
     */
    public function between($value, $lower, $upper)
    {
        return sprintf('%s BETWEEN %s AND %s', $value, $lower, $upper);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name', $qb->expr()->count('*'))
     *                 ->from('users', 'u');
     * </code>
     */
    public function count($name)
    {
        return sprintf('COUNT(%s)', $name);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->isNull('u.email'));
     * </code>
     */
    public function isNull($name)
    {
        return sprintf('%s IS NULL', $name);
    }
    
    /**
     * {@link inheritDoc}
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->isNotNull('u.gender'));
     * </code>
     */
    public function isNotNull($name)
    {
        return sprintf('%s IS NOT NULL', $name);
    }
    
    /**
     * Set the {@link QueryBuilderInterface} instance.
     *
     * @param QueryBuilderInterface $builder the queryBuilder.
     */
    public function setQueryBuilder(QueryBuilderInterface $builder)
    {
        $this->builder = $builder;
    }
    
    /**
     * Returns a {@link QueryBuilderInterface} instance.
     *
     * @return QueryBuilderInterface the query builder.
     */
    public function getQueryBuilder()
    {
        return $this->builder;
    }
    
    /**
     * Returns a subquery from the specified callback.
     *
     * The callback receives a {@link QueryBuilderInterface} instance which can be used to create a subquery. 
     * The following code demonstrates the usage with an IN clause.
     *
     * <code>
     *    $ad = new Adapter(array('driver' => 'wpdb'));
     *    $qb = $ad->getQueryBuilder();
     *
     *    $select = $qb->select('u.name')
     *                 ->from('users', 'u')
     *                 ->where($qb->expr()->in('u.age', function($qb) {
     *                       // return the subquery.
     *                       return $qb->select('e.age')
     *                                 ->from('employees', 'e')
     *                                 ->where('e.gender', ':gender')
     *                                 ->limit(10);
     *                   }));
     * </code>
     *
     *
     * @param callable $callback the callback to invoke.
     * @return string the SQL statement that represents the subquery.
     * @throws UnexpectedResultException if the specified callback does not return {@link Select} instance.
     */
    private function createSubQuery(callable $callback)
    {
        $select = call_user_func($callback, $this->getQueryBuilder());
        if ($select instanceof Select) {
            throw new UnexpectedResultException(sprintf(
                '%s: expects a Select instance; received "%s" instead',
                __METHOD__,
                (is_object($select)) ? get_class($select) : gettype($select)
            ));
        }
        
        return $select->getSqlString();
    }
}
