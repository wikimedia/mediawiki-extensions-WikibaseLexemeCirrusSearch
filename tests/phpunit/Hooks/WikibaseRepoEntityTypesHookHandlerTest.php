<?php

declare( strict_types = 1 );

namespace Wikibase\Lexeme\Search\Elastic\Tests\Hooks;

use MediaWikiIntegrationTestCase;
use Wikibase\Lexeme\MediaWiki\Content\LexemeContent;
use Wikibase\Lexeme\Search\Elastic\Hooks\WikibaseRepoEntityTypesHookHandler;
use Wikibase\Lib\EntityTypeDefinitions;

/**
 * @covers \Wikibase\Lexeme\Search\Elastic\Hooks\WikibaseRepoEntityTypesHookHandler
 */
class WikibaseRepoEntityTypesHookHandlerTest extends MediaWikiIntegrationTestCase {

	public function testDoesNothingIfDisabled(): void {
		$this->overrideConfigValue( 'LexemeUseCirrus', null );
		$entityTypeDefinitions = [ 'item' => [], 'property' => [] ];
		$original = $entityTypeDefinitions; // copy
		$handler = new WikibaseRepoEntityTypesHookHandler();

		$handler->onWikibaseRepoEntityTypes( $entityTypeDefinitions );

		$this->assertSame( $original, $entityTypeDefinitions );
	}

	public function testOverridesCallbacks(): void {
		$this->overrideConfigValue( 'LexemeUseCirrus', true );
		$entityTypeDefinitions = [
			'lexeme' => [
				EntityTypeDefinitions::CONTENT_MODEL_ID => LexemeContent::CONTENT_MODEL_ID,
				EntityTypeDefinitions::ENTITY_SEARCH_CALLBACK => 'original lexeme callback',
			],
			'form' => [
				EntityTypeDefinitions::ENTITY_SEARCH_CALLBACK => 'original form callback',
			],
			'sense' => [
				EntityTypeDefinitions::ENTITY_SEARCH_CALLBACK => 'original sense callback',
			],
		];
		$handler = new WikibaseRepoEntityTypesHookHandler();

		$handler->onWikibaseRepoEntityTypes( $entityTypeDefinitions );

		$this->assertSame( LexemeContent::CONTENT_MODEL_ID,
			$entityTypeDefinitions['lexeme'][EntityTypeDefinitions::CONTENT_MODEL_ID] );
		$lexemeCallback = $entityTypeDefinitions['lexeme'][EntityTypeDefinitions::ENTITY_SEARCH_CALLBACK];
		$this->assertNotSame( 'original lexeme callback', $lexemeCallback );
		$this->assertIsCallable( $lexemeCallback );
		$formCallback = $entityTypeDefinitions['form'][EntityTypeDefinitions::ENTITY_SEARCH_CALLBACK];
		$this->assertNotSame( 'original form callback', $formCallback );
		$this->assertIsCallable( $formCallback );
		$senseCallback = $entityTypeDefinitions['form'][EntityTypeDefinitions::ENTITY_SEARCH_CALLBACK];
		$this->assertNotSame( 'original form callback', $senseCallback );
		$this->assertIsCallable( $senseCallback );
	}

	public function testKeepsEntityTypeOrder(): void {
		$this->overrideConfigValue( 'LexemeUseCirrus', true );
		$entityTypeDefinitions = [
			'item' => [],
			'property' => [],
			'lexeme' => [],
			'form' => [],
			'sense' => [],
		];
		$handler = new WikibaseRepoEntityTypesHookHandler();

		$handler->onWikibaseRepoEntityTypes( $entityTypeDefinitions );

		$this->assertSame( [ 'item', 'property', 'lexeme', 'form', 'sense' ], array_keys( $entityTypeDefinitions ) );
	}

}
