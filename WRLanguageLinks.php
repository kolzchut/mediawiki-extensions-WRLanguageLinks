<?php
/**
 * WRLanguageLinks extension - Provides interlanguage links
 * @author Dror Snir
 * @copyright (C) 2006 Dror Snir (Kol-Zchut)
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/* Setup */
$wgExtensionCredits['parserhook'][] = array(
    'path'           => __FILE__,
    'name'           => 'Kol-Zchut Language Links',
    'author'         => 'Dror Snir ([http://www.kolzchut.org.il Kol-Zchut])',
    'version'        => '1.0.2',
    'url'            => 'http://www.kolzchut.org.il/he/Project:Extensions/WRLanguageLinks',
    'descriptionmsg' => 'wrlanguagelinks-desc',
);

$wgWRLanguageLinksShowOnly = null;
$wgWRLanguageLinksShowTitles = false; //Show pagename instead of language name; namely <a title=langname>pagename</a>, instead of the opposite
$wgWRLanguageLinksListType = 'normal'; //Other options: flat (inline list)

// Internationalization
$wgExtensionMessagesFiles['WRLanguageLinks'] = __DIR__ . '/WRLanguageLinks.i18n.php';
$wgExtensionMessagesFiles['WRLanguageLinksMagic'] = __DIR__ . '/WRLanguageLinks.i18n.magic.php';

// Auto load of classes
$wgAutoloadClasses['WRLanguageLinksHooks'] = __DIR__ . '/WRLanguageLinks.hooks.php';
$wgAutoloadClasses['WRLanguageLinks'] = __DIR__ . '/WRLanguageLinks.classes.php';

// Register hooks
$wgHooks['ParserFirstCallInit'][] = 'WRLanguageLinksHooks::register';
$wgHooks['ParserBeforeTidy'][] = 'WRLanguageLinksHooks::render';
