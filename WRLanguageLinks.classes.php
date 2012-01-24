<?php
/**
* Classes for WRLanguageLinks extension
*
* @file
* @ingroup Extensions
*/

// WRLanguageLinks class
class WRLanguageLinks {
	/* Fields */
	
	private $mParser;
	const markerText = 'x--WRLanguageLinks-marker--x';
	const markerHasLinksText = 'x--WRLanguageLinks-has-links-marker--x';
	
	/* Functions */ 
	
	public function __construct( $parser ) {
		$this->mParser = $parser;
	}
	
	public function renderMarker() {
		return self::markerText;
	}
	
	public function renderHasLinksMarker() {
		return self::markerHasLinksText;
	}
	
	public function render( &$text ) {
		// find markers in $text and replace with actual output
		$text = str_replace( self::markerText, $this->getLanguageLinks(), $text ); // Find markers for language links
		$text = str_replace( self::markerHasLinksText, $this->hasLanguageLinks(), $text);	  // Find markers for hasLanguageLinks	
		return true;
	}
	
	private function hasLanguageLinks() {
		global $wgWRLanguageLinksShowOnly;
		$parserLanguageLinks = $this->mParser->getOutput()->getLanguageLinks();
		$showOnly = explode( ',', $wgWRLanguageLinksShowOnly );
		if( $parserLanguageLinks == null ) {
			return "false"; // no language links at all, valid or otherwise
		} elseif( count( $showOnly ) == 0 ) {
			return "true"; // there are some language links, and all links are considered valid
		} else {
			foreach( $parserLanguageLinks as $l ) {
				$tmp = explode( ':', $l, 2 );
				if( in_array( $tmp[0], $showOnly ) ) return "true";	// there is at least one valid language link
			}
		}
		return "false"; // if we got this far, there are no valid language links
	}
	
	private function getLanguageLinks() {
		global $wgContLang, $wgWRLanguageLinksShowOnly;
		$output = null;
		# Language links - ripped from SkinTemplate.php
		$parserLanguageLinks = $this->mParser->getOutput()->getLanguageLinks();
		$language_urls = array();
		
		if( $wgWRLanguageLinksShowOnly != null ) {
			$showOnly = explode( ',', $wgWRLanguageLinksShowOnly );
		}
		foreach( $parserLanguageLinks as $l ) {
			$tmp = explode( ':', $l, 2 );
			if( count( $showOnly ) == 0 || in_array( $tmp[0], $showOnly ) ) {
				$class = 'wr-languagelinks-' . $tmp[0];
				unset( $tmp );
				$nt = Title::newFromText( $l );
				if ( $nt ) {
					$language_urls[] = array(
						'href' => $nt->getFullURL(),
						'title' => ( $wgContLang->getLanguageName( $nt->getInterwiki() ) != '' ?
									$wgContLang->getLanguageName( $nt->getInterwiki() ) : $l ),
						'text' => $nt->getText(),
						'class' => $class
					);
				}
			}
		}

		if( count( $language_urls ) ) {
			$output = '<div class="wr-languagelinks" style="max-width: 80%; margin: auto; background-color: white; border: 1px solid #CEDFF2">' . 
			'<div class="wr-languagelinks-title" style="text-align: center; font-weight: bold;">' . wfMsg( 'otherlanguages' ) . ':</div>' . 
				'<ul class="wr-languagelinks-list">';
			foreach ( $language_urls as $langlink ) {
				$output .= '<li class="'. htmlspecialchars(  $langlink['class'] ) . '"><a href="' . htmlspecialchars( $langlink['href'] ) . '" title="' . htmlspecialchars( $langlink['title'] ) . '">' . $langlink['text'] . '</a></li>';
			}
			$output .= '</ul></div>';
		}
		
		return $output;
	}

}

		
