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

	/**
	 * MakeGlobalVariablesScript hook handler
	 * For values that depend on the current page, user or request state.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/MakeGlobalVariablesScript
	 * @param array &$vars Variables to be added into the output
	 * @param OutputPage $out instance calling the hook
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		$langLinks = $out->getLanguageLinks();
		$langLinksArray = [];

		foreach ( $langLinks as $link ) {
			$explodedLink = explode( ':', $link, 2 );
			$langLinksArray[ $explodedLink[0] ] = [
				'title' => $explodedLink[1],
				'url' => Title::newFromText( $link )->getFullURL()
			];
		}

		$vars['wgLanguageLinks'] = $langLinksArray;
	}
}
