<?php

use MediaWiki\MediaWikiServices;
use Wikibase\DataModel\Services\Lookup\InProcessCachingDataTypeLookup;
use Wikibase\Lexeme\DataAccess\Store\NullLabelDescriptionLookup;
use Wikibase\Lexeme\Search\Elastic\FormSearchEntity;
use Wikibase\Lexeme\Search\Elastic\LexemeFieldDefinitions;
use Wikibase\Lexeme\Search\Elastic\LexemeFullTextQueryBuilder;
use Wikibase\Lexeme\Search\Elastic\LexemeSearchEntity;
use Wikibase\Lib\EntityTypeDefinitions as Def;
use Wikibase\Lib\SettingsArray;
use Wikibase\Lib\Store\LanguageFallbackLabelDescriptionLookup;
use Wikibase\Repo\Api\CombinedEntitySearchHelper;
use Wikibase\Repo\Api\EntityIdSearchHelper;
use Wikibase\Repo\WikibaseRepo;
use Wikibase\Search\Elastic\Fields\StatementProviderFieldDefinitions;

return [
	'lexeme' => [
		Def::SEARCH_FIELD_DEFINITIONS => function ( array $languageCodes, SettingsArray $searchSettings ) {
			$repo = WikibaseRepo::getDefaultInstance();
			$config = MediaWikiServices::getInstance()->getMainConfig();
			if ( $config->has( 'LexemeLanguageCodePropertyId' ) ) {
				$lcID = $config->get( 'LexemeLanguageCodePropertyId' );
			} else {
				$lcID = null;
			}
			return new LexemeFieldDefinitions(
				StatementProviderFieldDefinitions::newFromSettings(
					new InProcessCachingDataTypeLookup( $repo->getPropertyDataTypeLookup() ),
					$repo->getDataTypeDefinitions()->getSearchIndexDataFormatterCallbacks(),
					$searchSettings
				),
				$repo->getEntityLookup(),
				$lcID ? $repo->getEntityIdParser()->parse( $lcID ) : null
			);
		},
		Def::ENTITY_SEARCH_CALLBACK => function ( WebRequest $request ) {
			$repo = WikibaseRepo::getDefaultInstance();
			return new CombinedEntitySearchHelper(
				[
					new EntityIdSearchHelper(
						$repo->getEntityLookup(),
						$repo->getEntityIdParser(),
						new LanguageFallbackLabelDescriptionLookup(
							$repo->getTermLookup(),
							$repo->getLanguageFallbackChainFactory()->newFromLanguage( $repo->getUserLanguage() )
						),
						$repo->getEntityTypeToRepositoryMapping()
					),
					new LexemeSearchEntity(
						$repo->getEntityIdParser(),
						$request,
						$repo->getUserLanguage(),
						$repo->getLanguageFallbackChainFactory(),
						$repo->getPrefetchingTermLookup()
					)
				]
			);
		},
		Def::FULLTEXT_SEARCH_CONTEXT => LexemeFullTextQueryBuilder::CONTEXT_LEXEME_FULLTEXT,
	],
	'form' => [
		Def::ENTITY_SEARCH_CALLBACK => function ( WebRequest $request ) {
			$repo = WikibaseRepo::getDefaultInstance();
			return new CombinedEntitySearchHelper(
				[
					new Wikibase\Repo\Api\EntityIdSearchHelper(
						$repo->getEntityLookup(),
						$repo->getEntityIdParser(),
						new NullLabelDescriptionLookup(),
						$repo->getEntityTypeToRepositoryMapping()
					),
					new FormSearchEntity(
						$repo->getEntityIdParser(),
						$request,
						$repo->getUserLanguage(),
						$repo->getLanguageFallbackChainFactory(),
						$repo->getPrefetchingTermLookup()
					),
				]
			);
		},
	],
	// TODO: support senses?
];
