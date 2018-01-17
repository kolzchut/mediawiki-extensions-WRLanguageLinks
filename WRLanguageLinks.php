<?php
/**
 * WRLanguageLinks extension - Provides interlanguage links
 * @author Dror S. [FFS]
 * @copyright © 2014-2015 Dror S. & Kol-Zchut Ltd.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/* Setup */
$wgExtensionCredits['parserhook'][] = [
	'path'           => __FILE__,
	'name'           => 'Kol-Zchut Language Links',
	'author'         => 'Dror S. [FFS] ([http://www.kolzchut.org.il Kol-Zchut])',
	'version'        => '1.0.2',
	'url'            => 'https://github.com/kolzchut/mediawiki-extensions-WRLanguageLinks',
	'license-name'    => 'GPL-2.0+',
	'descriptionmsg' => 'wrlanguagelinks-desc',
];

$wgWRLanguageLinksShowOnly = null;

// Show pagename instead of language name
$wgWRLanguageLinksShowTitles = false;

// Other options: flat (inline list)
$wgWRLanguageLinksListType = 'normal';

// Show a "translations" / "language links" label for the list
$wgWRLanguageLinksShowListLabel = true;

// Internationalization
$GLOBALS['wgMessagesDirs']['WRLanguageLinks'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['WRLanguageLinksMagic'] = __DIR__ . '/WRLanguageLinks.i18n.magic.php';

// Auto load of classes
$wgAutoloadClasses['WRLanguageLinksHooks'] = __DIR__ . '/WRLanguageLinks.hooks.php';
$wgAutoloadClasses['WRLanguageLinks'] = __DIR__ . '/WRLanguageLinks.classes.php';

// Register hooks
$wgHooks['ParserFirstCallInit'][] = 'WRLanguageLinksHooks::register';
$wgHooks['ParserBeforeTidy'][] = 'WRLanguageLinksHooks::render';
