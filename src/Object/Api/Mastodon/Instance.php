<?php

// Copyright (C) 2010-2024, the Friendica project
// SPDX-FileCopyrightText: 2010-2024 the Friendica project
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace Friendica\Object\Api\Mastodon;

use Friendica\App;
use Friendica\App\BaseURL;
use Friendica\BaseDataTransferObject;
use Friendica\Contact\Header;
use Friendica\Core\Config\Capability\IManageConfigValues;
use Friendica\Database\Database;
use Friendica\Model\User;
use Friendica\Module\Register;
use Friendica\Object\Api\Mastodon\InstanceV2\Configuration;

/**
 * Class Instance
 *
 * @see https://docs.joinmastodon.org/entities/V1_Instance/
 */
class Instance extends BaseDataTransferObject
{
	/** @var string (URL) */
	protected $uri;
	/** @var string */
	protected $title;
	/** @var string */
	protected $short_description;
	/** @var string */
	protected $description;
	/** @var string */
	protected $email;
	/** @var string */
	protected $version;
	/** @var array */
	protected $urls;
	/** @var Stats */
	protected $stats;
	/** @var string|null This is meant as a server banner, default Mastodon "thumbnail" is 1600×620px */
	protected $thumbnail = null;
	/** @var array */
	protected $languages;
	/** @var int */
	protected $max_toot_chars;
	/** @var bool */
	protected $registrations;
	/** @var bool */
	protected $approval_required;
	/** @var bool */
	protected $invites_enabled;
	/** @var Configuration  */
	protected $configuration;
	/** @var Account|null */
	protected $contact_account = null;
	/** @var array */
	protected $rules = [];

	public function __construct(IManageConfigValues $config, BaseURL $baseUrl, Database $database, Configuration $configuration, ?Account $contact_account, array $rules)
	{
		$register_policy = Register::getPolicy();

		$this->uri               = $baseUrl->getHost();
		$this->title             = $config->get('config', 'sitename');
		$this->short_description = $this->description = $config->get('config', 'info');
		$this->email             = implode(',', User::getAdminEmailList());
		$this->version           = '2.8.0 (compatible; Friendica ' . App::VERSION . ')';
		$this->urls              = ['streaming_api' => '']; // Not supported
		$this->stats             = new Stats($config, $database);
		$this->thumbnail         = $baseUrl . (new Header($config))->getMastodonBannerPath();
		$this->languages         = [$config->get('system', 'language')];
		$this->max_toot_chars    = (int)$config->get('config', 'api_import_size', $config->get('config', 'max_import_size'));
		$this->registrations     = ($register_policy !== Register::CLOSED);
		$this->approval_required = ($register_policy === Register::APPROVE);
		$this->invites_enabled   = false;
		$this->configuration     = $configuration;
		$this->contact_account   = $contact_account ?? [];
		$this->rules             = $rules;
	}
}
