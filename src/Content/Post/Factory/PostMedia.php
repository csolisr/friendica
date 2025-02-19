<?php

// Copyright (C) 2010-2024, the Friendica project
// SPDX-FileCopyrightText: 2010-2024 the Friendica project
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace Friendica\Content\Post\Factory;

use Friendica\BaseFactory;
use Friendica\Capabilities\ICanCreateFromTableRow;
use Friendica\Content\Post\Entity;
use Friendica\Network;
use Friendica\Util\Network as UtilNetwork;
use GuzzleHttp\Psr7\Uri;
use Psr\Log\LoggerInterface;
use stdClass;

class PostMedia extends BaseFactory implements ICanCreateFromTableRow
{
	/** @var Network\Factory\MimeType */
	private $mimeTypeFactory;

	public function __construct(Network\Factory\MimeType $mimeTypeFactory, LoggerInterface $logger)
	{
		parent::__construct($logger);

		$this->mimeTypeFactory = $mimeTypeFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function createFromTableRow(array $row)
	{
		return new Entity\PostMedia(
			$row['uri-id'],
			UtilNetwork::createUriFromString($row['url']),
			$row['type'],
			$this->mimeTypeFactory->createFromContentType($row['mimetype']),
			$row['media-uri-id'],
			$row['width'],
			$row['height'],
			$row['size'],
			UtilNetwork::createUriFromString($row['preview']),
			$row['preview-width'],
			$row['preview-height'],
			$row['description'],
			$row['name'],
			UtilNetwork::createUriFromString($row['author-url']),
			$row['author-name'],
			UtilNetwork::createUriFromString($row['author-image']),
			UtilNetwork::createUriFromString($row['publisher-url']),
			$row['publisher-name'],
			UtilNetwork::createUriFromString($row['publisher-image']),
			$row['blurhash'],
			$row['id']
		);
	}

	public function createFromBlueskyImageEmbed(int $uriId, stdClass $image): Entity\PostMedia
	{
		return new Entity\PostMedia(
			$uriId,
			new Uri($image->fullsize),
			Entity\PostMedia::TYPE_IMAGE,
			new Network\Entity\MimeType('unkn', 'unkn'),
			null,
			null,
			null,
			null,
			new Uri($image->thumb),
			null,
			null,
			$image->alt,
		);
	}


	public function createFromBlueskyExternalEmbed(int $uriId, stdClass $external): Entity\PostMedia
	{
		return new Entity\PostMedia(
			$uriId,
			new Uri($external->uri),
			Entity\PostMedia::TYPE_HTML,
			new Network\Entity\MimeType('text', 'html'),
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			$external->description,
			$external->title
		);
	}

	public function createFromAttachment(int $uriId, array $attachment)
	{
		$attachment['uri-id'] = $uriId;
		return $this->createFromTableRow($attachment);
	}
}
