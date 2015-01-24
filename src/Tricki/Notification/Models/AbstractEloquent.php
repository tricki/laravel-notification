<?php

namespace Tricki\Notification\Models;

use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Builder;

/*
 * Abstract class allowing for Single Table Inheritence
 */

abstract class AbstractEloquent extends Eloquent\Model
{

	protected $isSuperType = false; // set true in super-class model
	protected $isSubType = false; // set true in inherited models
	protected $typeField = 'type'; //override as needed, only set on the super-class model

	/**
	 * Provide an attributes to object map
	 *
	 * @return Model
	 */

	public function mapData(array $attributes)
	{
		if (!$this->isSuperType)
		{
			return $this->newInstance();
		}
		else
		{
			if (!isset($attributes[$this->typeField]))
			{
				throw new \DomainException($this->typeField . ' not present in the records of a Super Model');
			}
			else
			{
				$class = $this->getClass($attributes[$this->typeField]);
				return new $class;
			}
		}
	}

	/**
	 * Create a new model instance requested by the builder.
	 *
	 * @param  array  $attributes
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function newFromBuilder($attributes = array())
	{
		$m = $this->mapData((array) $attributes)->newInstance(array(), true);
		$m->setRawAttributes((array) $attributes, true);
		return $m;
	}

	/**
	 * Get a new query builder for the model's table.
	 *
	 * @return Reposed\Builder
	 */
	public function newRawQuery()
	{
		$builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

		// Once we have the query builders, we will set the model instances
		// so the builder can easily access any information it may need
		// from the model while it is constructing and executing various
		// queries against it.
		$builder->setModel($this)->with($this->with);
		return $builder;
	}

	/**
	 * Get a new query builder for the model.
	 * set any type of scope you want on this builder in a child class, and it'll
	 * keep applying the scope on any read-queries on this model
	 *
	 * @return Reposed\Builder
	 */
	public function newQuery($excludeDeleted = true)
	{
		$builder = parent::newQuery($excludeDeleted);

		if ($this->isSubType())
		{
			$builder->where($this->typeField, $this->getClass($this->typeField));
		}

		return $builder;
	}

	protected function isSubType()
	{
		return $this->isSubType;
	}

	protected function getClass($type)
	{
		return get_class($this);
	}

	protected function getType()
	{
		return get_class($this);
	}

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save(array $options = array())
	{
		if ($this->isSubType())
		{
			$this->attributes[$this->typeField] = $this->getType();
		}

		return parent::save($options);
	}

}
