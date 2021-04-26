<?php

class WRLanguageLinksHooks {

	/**
	 * Hook: ParserFirstCallInit
	 * @param $parser Parser
	 * @return true
	 */
	public static function register( &$parser ) {
		$parser->setHook( 'languagelinks', [ 'WRLanguageLinks', 'renderMarker' ] );
		$parser->setFunctionHook(
			'haslanguagelinks',
			[ 'WRLanguageLinks', 'renderHasLinksMarker' ]
		);
		return true;
	}

	/**
	 * Hook: ParserAfterTidy
	 * @param $parser Parser
	 * @param $text string
	 */
	public static function onParserAfterTidy( &$parser, &$text ) {
		// Create LangBox
		$wrLanguageLinks = new WRLanguageLinks( $parser );

		// Return output
		$wrLanguageLinks->render( $text );
	}
}
