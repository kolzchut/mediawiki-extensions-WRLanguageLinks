<?php

class WRLanguageLinksHooks {

	/**
	 * Hook: ParserFirstCallInit
	 * @param $parser Parser
	 * @return true
	 */
	public static function register( &$parser ) {
		$parser->setHook( 'languagelinks', array( 'WRLanguageLinks', 'renderMarker' ) );
		$parser->setFunctionHook(
			'haslanguagelinks',
			[ 'WRLanguageLinks', 'renderHasLinksMarker' ]
		);
		return true;
	}

	/**
	 * Hook: ParserBeforeTidy
	 * @param $parser Parser
	 * @param $text string
	 * @return true
	 */
	public static function render( &$parser, &$text ) {
		// Create LangBox
		$wrLanguageLinks = new WRLanguageLinks( $parser );

		// Return output
		$wrLanguageLinks->render( $text );

		return true;
	}
}
