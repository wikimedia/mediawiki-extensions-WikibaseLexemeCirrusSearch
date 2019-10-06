<?php

namespace Wikibase\Lexeme\Search\Elastic\Tests;

use PHPUnit\Framework\TestCase;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\EntityLookup;
use Wikibase\Lexeme\Search\Elastic\LexemeFieldDefinitions;
use Wikibase\Search\Elastic\Fields\StatementProviderFieldDefinitions;

/**
 * @covers \Wikibase\Lexeme\Search\Elastic\LexemeFieldDefinitions
 *
 * @license GPL-2.0-or-later
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class LexemeFieldDefinitionsTest extends TestCase {

	public function testGetFields() {
		$fieldDefinitions = new LexemeFieldDefinitions(
			$this->getMockStatementProviderFieldDefinitions(),
			$this->createMock( EntityLookup::class ),
			new PropertyId( 'P123' )
		);

		$this->assertHasLexemeFields( $fieldDefinitions->getFields() );
	}

	public function testGetFieldsNoCode() {
		$fieldDefinitions = new LexemeFieldDefinitions(
			$this->getMockStatementProviderFieldDefinitions(),
			$this->createMock( EntityLookup::class ),
			null
		);

		$this->assertHasLexemeFields( $fieldDefinitions->getFields() );
	}

	private function assertHasLexemeFields( array $actualFields ) {
		$this->assertArrayHasKey( 'lemma', $actualFields );
		$this->assertArrayHasKey( 'lexeme_forms', $actualFields );
		$this->assertArrayHasKey( 'lexeme_language', $actualFields );
		$this->assertArrayHasKey( 'lexical_category', $actualFields );
	}

	private function getMockStatementProviderFieldDefinitions() {
		$definitions = $this->getMockBuilder( StatementProviderFieldDefinitions::class )
			->disableOriginalConstructor()
			->getMock();
		$definitions
			->method( 'getFields' )
			->willReturn( [] );
		return $definitions;
	}

}
