<?php

namespace Wikibase\Lexeme\Search\Elastic\Tests;

use Wikibase\Lexeme\Search\Elastic\LemmaSpellingVariantsField;

/**
 * @covers \Wikibase\Lexeme\Search\Elastic\LemmaField
 */
class LemmaSpellingVariantsFieldTest extends LexemeFieldTestBase {

	/**
	 * @return array
	 */
	public function getTestData() {
		return [
			[
				new LemmaSpellingVariantsField(),
				[ "en", "en-gb" ]
			]
		];
	}

}
