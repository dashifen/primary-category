<?php

namespace Dashifen\PrimaryCategory\Containers;

use Dashifen\Container\Container;

/**
 * Class Category
 * @package Dashifen\PrimaryCategory\Categories
 * @property $id
 * @property $name;
 * @property $isPrimary;
 */
class Category extends Container {
	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @var string
	 */
	protected $name = "";

	/**
	 * @var bool
	 */
	protected $isPrimary = false;

	public function __construct(int $id, string $name, bool $isPrimary) {
		parent::__construct([
			"id"        => $id,
			"name"      => $name,
			"isPrimary" => $isPrimary,
		]);
	}

	/**
	 * setId
	 *
	 * Sets the id property.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function setId(int $id): void {
		$this->id = $id;
	}

	/**
	 * setName
	 *
	 * Sets the name property.
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName(string $name): void {
		$this->name = $name;
	}

	/**
	 * setIsPrimary
	 *
	 * Sets the isPrimary property.
	 *
	 * @param bool $isPrimary
	 *
	 * @return void
	 */
	public function setIsPrimary(bool $isPrimary): void {
		$this->isPrimary = $isPrimary;
	}
}