<?php

use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use Wikibase\Lexeme\Search\Elastic\LexemeSearchEntity;
use Wikibase\Repo\Api\CombinedEntitySearchHelper;
use Wikibase\Repo\Api\EntityIdSearchHelper;
use Wikibase\Repo\Api\EntitySearchHelper;
use Wikibase\Repo\WikibaseRepo;

/** @phpcs-require-sorted-array */
return [
	'WikibaseLexemeCirrusSearch.LexemeSearchHelper' => static function (
		MediaWikiServices $services
	): EntitySearchHelper {
		$fallbackTermLookupFactory = WikibaseRepo::getFallbackLabelDescriptionLookupFactory( $services );
		$entityIdParser = WikibaseRepo::getEntityIdParser( $services );
		$language = RequestContext::getMain()->getLanguage();

		return new CombinedEntitySearchHelper( [
			new LexemeSearchEntity(
				$entityIdParser,
				RequestContext::getMain()->getRequest(),
				$language,
				$fallbackTermLookupFactory
			),
			new EntityIdSearchHelper(
				WikibaseRepo::getEntityLookup( $services ),
				$entityIdParser,
				$fallbackTermLookupFactory->newLabelDescriptionLookup( $language ),
				WikibaseRepo::getEnabledEntityTypes( $services )
			),
		] );
	},
];
