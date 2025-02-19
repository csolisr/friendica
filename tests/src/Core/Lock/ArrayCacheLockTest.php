<?php

// Copyright (C) 2010-2024, the Friendica project
// SPDX-FileCopyrightText: 2010-2024 the Friendica project
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace Friendica\Test\src\Core\Lock;

use Friendica\Core\Cache\Type\ArrayCache;
use Friendica\Core\Lock\Type\CacheLock;

class ArrayCacheLockTest extends LockTest
{
	protected function getInstance()
	{
		return new \Friendica\Core\Lock\Type\CacheLock(new ArrayCache('localhost'));
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testLockTTL()
	{
		self::markTestSkipped("ArrayCache doesn't support TTL");
	}
}
