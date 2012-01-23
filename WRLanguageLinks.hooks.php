<?php

class WRLanguageLinksHooks {
	
	public static function register( &$parser ) {
		$parser->setHook( 'languagelinks', array( 'WRLanguageLinksHooks', 'renderMarker' ) );
		return true;
	}

	public function renderMarker() {
		return WRLanguageLinks::renderMarker();
	}
	
	/**
	 * @param $parser Parser
	 * @param $alt string
	 * @return String
	 */
	public static function render( &$parser, &$text ) {
		// Create LangBox
		$wrLanguageLinks = new WRLanguageLinks( $parser );
		
		// Return output
		$wrLanguageLinks->render( $text );
		
		return true;
	}
}
