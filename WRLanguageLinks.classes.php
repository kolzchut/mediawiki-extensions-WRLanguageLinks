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
	
	/* Functions */ 
	
	public function __construct( $parser ) {
		$this->mParser = $parser;
	}
	
	public function renderMarker() {
		return self::markerText;
	}
	
	public function render( &$text ) {
		// find markers in $text and replace with actual output
		$count = 0;
		$text = str_replace( self::markerText, $this->getLanguageLinks(), $text, $count );
		return true;
	}
	
	
	private function getLanguageLinks() {
		global $wgContLang, $wgWRLanguageLinksShowOnly;
		$output = '';
		# Language links - ripped from SkinTemplate.php
		$parserLanguageLinks = $this->mParser->getOutput()->getLanguageLinks();
		$language_urls = array();
		
		if( $wgWRLanguageLinksShowOnly != null ) {
			$showOnly = explode( ',', $wgWRLanguageLinksShowOnly );
		}
		foreach( $parserLanguageLinks as $l ) {
			$tmp = explode( ':', $l, 2 );
			if( count( $showOnly ) == 0 || in_array( $tmp[0], $showOnly ) ) {
				$class = 'wr-interwiki-' . $tmp[0];
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
			$output = '<ul class="wr-interwiki">';
			foreach ( $language_urls as $langlink ) {
				$output .= '<li class="'. htmlspecialchars(  $langlink['class'] ) . '"><a href="' . htmlspecialchars( $langlink['href'] ) . '" title="' . htmlspecialchars( $langlink['title'] ) . '">' . $langlink['text'] . '</a></li>';
			}
			$output .= '</ul>';
		} else { 
			$output = '';	/* wfMsg('wrlanguagelinks-nolinks'); */
		}
		
		return $output;
	}

}

		
