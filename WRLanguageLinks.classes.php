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
	const MARKER_TEXT = 'x--WRLanguageLinks-marker--x';
	const MARKER_HAS_LINKS_TEXT = 'x--WRLanguageLinks-has-links-marker--x';

	/* Functions */

	public function __construct( Parser $parser ) {
		$this->mParser = $parser;
	}

	public static function renderMarker() {
		return self::MARKER_TEXT;
	}

	public static function renderHasLinksMarker() {
		return self::MARKER_HAS_LINKS_TEXT;
	}

	/*
	 * find markers in $text and replace with actual output
	 */
	public function render( &$text ) {
		// Find markers for language links
		$text = str_replace( self::MARKER_TEXT, $this->getLanguageLinks(), $text );
		// Find markers for hasLanguageLinks
		$text = str_replace( self::MARKER_HAS_LINKS_TEXT, $this->hasLanguageLinks(), $text );
		return true;
	}

	private function hasLanguageLinks() {
		global $wgWRLanguageLinksShowOnly;
		$parserLanguageLinks = $this->mParser->getOutput()->getLanguageLinks();
		$showOnly = explode( ',', $wgWRLanguageLinksShowOnly );
		if ( $parserLanguageLinks == null ) {
			return "false"; // no language links at all, valid or otherwise
		} elseif ( count( $showOnly ) == 0 ) {
			return "true"; // there are some language links, and all links are considered valid
		} else {
			foreach ( $parserLanguageLinks as $l ) {
				$tmp = explode( ':', $l, 2 );
				if ( in_array( $tmp[0], $showOnly ) ) {
					// there is at least one valid language link
					return "true";
				}
			}
		}
		return "false"; // if we got this far, there are no valid language links
	}

	private function makeLanguageLinks() {
		global $wgWRLanguageLinksShowOnly, $wgWRLanguageLinksShowTitles;

		// Language links - ripped from SkinTemplate.php and then mangled badly
		$parserLanguageLinks = $this->mParser->getOutput()->getLanguageLinks();
		$language_urls = [];
		$showOnly = [];

		if ( $wgWRLanguageLinksShowOnly != null ) {
			$showOnly = explode( ',', $wgWRLanguageLinksShowOnly );
		}

		foreach ( $parserLanguageLinks as $languageLinkText ) {
			$languageLinkTitle = Title::newFromText( $languageLinkText );
			if ( $languageLinkTitle ) {
				$ilInterwikiCode = $languageLinkTitle->getInterwiki();

				if ( is_null( $wgWRLanguageLinksShowOnly ) || in_array( $ilInterwikiCode, $showOnly ) ) {
					$ilLangName = Language::fetchLanguageName( $ilInterwikiCode );
					if ( strval( $ilLangName ) === '' ) {
						$ilLangName = $languageLinkText;
					}
					$ilArticleName = $languageLinkTitle->getText();
					$language_urls[] = [
						'href' => $languageLinkTitle->getFullURL(),
						'text' => $wgWRLanguageLinksShowTitles ? $ilArticleName : $ilLangName,
						'title' => $wgWRLanguageLinksShowTitles ? $ilLangName : $ilArticleName,
						'class' => "wr-languagelinks-$ilInterwikiCode",
						'lang' => $ilInterwikiCode
					];
				}
			}
		}

		return $language_urls;
	}


	private function getLanguageLinks() {
		global $wgWRLanguageLinksListType, $wgWRLanguageLinksShowListLabel;

		$languageLinks = $this->makeLanguageLinks();
		if ( count( $languageLinks ) === 0 ) {
			return '';
		}
		$hasSingleLink = ( count( $languageLinks ) === 1 );

		$class = 'wr-languagelinks' . ( $hasSingleLink ? ' single-link' : '' );
		$output = Html::openElement( 'li' );	// Done to fit inside "See Also"
		$output .= Html::openElement( 'div', [ 'class' => $class ] );

		if ( $wgWRLanguageLinksShowListLabel === true ) {
			$label = wfMessage( 'wr-langlinks-label' )->numParams( count( $languageLinks ) )->text();
			$output .= Html::element( 'span', [ 'class' => 'wr-languagelinks-label' ], $label . ' ' );
		}

		$renderedLangLinks = [];
		foreach ( $languageLinks as $langLink ) {
			$renderedLangLinks[] = Html::element( 'a', [
					'class' => $langLink['class'],
					'href' => $langLink['href'],
					'title' => $langLink['title']
				],
				$langLink['text']
			);
		}

		if ( $hasSingleLink ) {
			$output .= '<span class="interlanguage-link">' . $renderedLangLinks[0] . '</span>';
		} else {
			$isInlineList = $wgWRLanguageLinksListType === 'flat';
			$class = $isInlineList ? 'list-inline' : null;
			$style = $isInlineList ? 'display: inline;' : null;

			$output .= '<ul class="'.$class.'" style="'.$style.'">';
			foreach ( $renderedLangLinks as $langLink ) {
				$output .= "<li class=\"interlanguage-link\">$langLink</li>";
			}
			$output .= Html::closeElement( 'ul' );
		}

		$output .= Html::closeElement( 'div' );	// Wrapper
		$output .= Html::closeElement( 'li' );	// li for "See Also"

		return $output;
	}

}


