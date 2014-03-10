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

    public function __construct( Parser $parser ) {
        $this->mParser = $parser;
    }

    public static function renderMarker() {
        return self::markerText;
    }

    public static function renderHasLinksMarker() {
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

    private function makeLanguageLinks() {
        global $wgWRLanguageLinksShowOnly, $wgWRLanguageLinksShowTitles;

        // Language links - ripped from SkinTemplate.php and then mangled badly
        $parserLanguageLinks = $this->mParser->getOutput()->getLanguageLinks();
        $language_urls = array();
        $showOnly = array();

        if( $wgWRLanguageLinksShowOnly != null ) {
            $showOnly = explode( ',', $wgWRLanguageLinksShowOnly );
        }

        foreach( $parserLanguageLinks as $languageLinkText ) {
            $languageLinkTitle = Title::newFromText( $languageLinkText );
            if ( $languageLinkTitle ) {
                $ilInterwikiCode = $languageLinkTitle->getInterwiki();

                if( is_null( $wgWRLanguageLinksShowOnly ) || in_array( $ilInterwikiCode, $showOnly ) ) {
                    $ilLangName = Language::fetchLanguageName( $ilInterwikiCode );
                    if ( strval( $ilLangName ) === '' ) {
                        $ilLangName = $languageLinkText;
                    }
                    $ilArticleName = $languageLinkTitle->getText();
                    $language_urls[] = array(
                        'href' => $languageLinkTitle->getFullURL(),
                        'text' => $wgWRLanguageLinksShowTitles ? $ilArticleName : $ilLangName,
                        'title' => $wgWRLanguageLinksShowTitles ? $ilLangName : $ilArticleName,
                        'class' => "wr-languagelinks-$ilInterwikiCode",
                        'lang' => $ilInterwikiCode
                    );
                }
            }
        }

        return $language_urls;
    }


    private function getLanguageLinks() {
        global $wgWRLanguageLinksListType, $wgLanguageCode;

        $languageLinks = $this->makeLanguageLinks();
        if( count( $languageLinks ) === 0 ) { return ''; };
        $hasSingleLink = ( count( $languageLinks ) === 1 );

        $class = 'wr-languagelinks' . ( $hasSingleLink ? ' single-link' : '' );
        $output = '<li><div class="' . $class .'"><span class="wr-languagelinks-title">';

        if( $hasSingleLink ) {
            $output .= wfMessage( 'wr-in-single-language', $languageLinks[0]['lang'] )->inContentLanguage()->text();

        } else {
            $output .= wfMessage( 'wr-otherlanguages' )->inContentLanguage()->text();
        }

        $class = ( $hasSingleLink || $wgWRLanguageLinksListType == 'flat' ) ? 'list-inline' : '';
        $style = $hasSingleLink ? ' style="display:inline"' : '';   // Saves on loading a CSS file
        $output .= '</span>' . '<ul class="' . $class . '"' . $style . '>';

        foreach( $languageLinks as $langLink) {
            $output .= '<li class="'. htmlspecialchars(  $langLink['class'] ) . '">' .
                '<a href="' . htmlspecialchars( $langLink['href'] ) . '" ' .
                'title="' . htmlspecialchars( $langLink['title'] ) . '">' . $langLink['text'] . '</a></li>';
        }

        $output .= '</ul></div></li>';
        //$this->mParser->getOutput()->addModules( 'ext.WRLanguageLinks' );

        return $output;
    }

}

		
