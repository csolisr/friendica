<?php
/**
 * @copyright Copyright (C) 2010-2023, the Friendica project
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace Friendica\Core\Hooks\Model;

use Dice\Dice;
use Friendica\Core\Hooks\Capabilities\ICanCreateInstances;
use Friendica\Core\Hooks\Capabilities\ICanRegisterInstances;
use Friendica\Core\Hooks\Exceptions\HookInstanceException;
use Friendica\Core\Hooks\Exceptions\HookRegisterArgumentException;
use Friendica\Core\Hooks\Util\HookFileManager;

/**
 * This class represents an instance register, which uses Dice for creation
 *
 * @see Dice
 */
class DiceInstanceManager implements ICanCreateInstances, ICanRegisterInstances
{
	protected $instance = [];

	/** @var Dice */
	protected $dice;

	public function __construct(Dice $dice, HookFileManager $hookFileManager)
	{
		$this->dice = $dice;
		$hookFileManager->setupHooks($this);
	}

	/** {@inheritDoc} */
	public function registerStrategy(string $interface, string $class, ?string $name = null): ICanRegisterInstances
	{
		if (!empty($this->instance[$interface][$name])) {
			throw new HookRegisterArgumentException(sprintf('A class with the name %s is already set for the interface %s', $name, $interface));
		}

		$this->instance[$interface][$name] = $class;

		return $this;
	}

	/** {@inheritDoc} */
	public function create(string $class, string $name, array $arguments = []): object
	{
		if (empty($this->instance[$class][$name])) {
			throw new HookInstanceException(sprintf('The class with the name %s isn\'t registered for the class or interface %s', $name, $class));
		}

		return $this->dice->create($this->instance[$class][$name], $arguments);
	}
}
