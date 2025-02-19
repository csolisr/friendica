<?php

// Copyright (C) 2010-2024, the Friendica project
// SPDX-FileCopyrightText: 2010-2024 the Friendica project
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace Friendica\Module\Api\Twitter\Blocks;

use Friendica\Core\System;
use Friendica\Database\DBA;
use Friendica\Module\Api\Twitter\ContactEndpoint;
use Friendica\Module\BaseApi;

/**
 * @see https://developer.twitter.com/en/docs/twitter-api/v1/accounts-and-users/mute-block-report-users/api-reference/get-blocks-list
 */
class Lists extends ContactEndpoint
{
	protected function rawContent(array $request = [])
	{
		$this->checkAllowedScope(self::SCOPE_READ);
		$uid = BaseApi::getCurrentUserID();

		// Expected value for user_id parameter: public/user contact id
		$cursor                = $this->getRequestValue($request, 'cursor', -1);
		$skip_status           = $this->getRequestValue($request, 'skip_status', false);
		$include_user_entities = $this->getRequestValue($request, 'include_user_entities', false);
		$count                 = $this->getRequestValue($request, 'count', self::DEFAULT_COUNT, 1, self::MAX_COUNT);

		// Friendica-specific
		$since_id = $this->getRequestValue($request, 'since_id', 0, 0);
		$max_id   = $this->getRequestValue($request, 'max_id', 0, 0);
		$min_id   = $this->getRequestValue($request, 'min_id', 0, 0);

		$params = ['order' => ['cid' => true], 'limit' => $count];

		$condition = ['uid' => $uid, 'blocked' => true];

		$total_count = (int)DBA::count('user-contact', $condition);

		if (!empty($max_id)) {
			$condition = DBA::mergeConditions($condition, ["`cid` < ?", $max_id]);
		}

		if (!empty($since_id)) {
			$condition = DBA::mergeConditions($condition, ["`cid` > ?", $since_id]);
		}

		if (!empty($min_id)) {
			$condition = DBA::mergeConditions($condition, ["`cid` > ?", $min_id]);

			$params['order'] = ['cid'];
		}

		$ids = [];

		$contacts = DBA::select('user-contact', ['cid'], $condition, $params);
		while ($contact = DBA::fetch($contacts)) {
			self::setBoundaries($contact['cid']);
			$ids[] = $contact['cid'];
		}
		DBA::close($contacts);

		if (!empty($min_id)) {
			$ids = array_reverse($ids);
		}

		$return = self::list($ids, $total_count, $uid, $cursor, $count, $skip_status, $include_user_entities);

		self::setLinkHeader();

		$this->response->addFormattedContent('lists', ['lists' => $return]);
	}
}
