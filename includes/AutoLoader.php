<?php
/**
 * This defines autoloading handling 
 *
 * @file
 */

/**
 * Locations of core classes
 * Extension classes are specified with $wgAutoloadClasses
 * This array is a global instead of a static member of AutoLoader to work around a bug in APC
 */
global $globAutoloadLocalClasses, $globAutoloadClasses;

$globAutoloadLocalClasses = array(
	# Includes
	'Sanitizer' => 'includes/Sanitizer.php',
	'WebRequest' => 'includes/WebRequest.php',
	'FauxRequest' => 'includes/WebRequest.php',
	'Hooks' => 'includes/Hooks.php',
	'Xml' => 'includes/Xml.php',

	# API base
	'ApiBase' => 'includes/apibase/ApiBase.php',
	'ApiFormatBase' => 'includes/apibase/ApiFormatBase.php',
	'ApiHelp' => 'includes/apibase/ApiHelp.php',
	'ApiMain' => 'includes/apibase/ApiMain.php',
	'ApiResult' => 'includes/apibase/ApiResult.php',

	# API formats
	'ApiFormatDbg' => 'includes/formats/ApiFormatDbg.php',
	'ApiFormatDump' => 'includes/formats/ApiFormatDump.php',
	'ApiFormatJson' => 'includes/formats/ApiFormatJson.php',
	'ApiFormatPhp' => 'includes/formats/ApiFormatPhp.php',
	'ApiFormatRaw' => 'includes/formats/ApiFormatRaw.php',
	'ApiFormatTxt' => 'includes/formats/ApiFormatTxt.php',
	'ApiFormatWddx' => 'includes/formats/ApiFormatWddx.php',
	'ApiFormatXml' => 'includes/formats/ApiFormatXml.php',
	'ApiFormatYaml' => 'includes/formats/ApiFormatYaml.php',

	# API modules
	'ApiTest' => 'includes/modules/ApiTest.php',
);

class AutoLoader {
	/**
	 * autoload - take a class name and attempt to load it
	 *
	 * @param $className String: name of class we're looking for.
	 * @return bool Returning false is important on failure as
	 * it allows Zend to try and look in other registered autoloaders
	 * as well.
	 */
	static function autoload( $className ) {
		global $globAutoloadLocalClasses, $globAutoloadClasses;

		if ( isset( $globAutoloadLocalClasses[$className] ) ) {
			$filename = $globAutoloadLocalClasses[$className];
		} elseif ( isset( $globAutoloadClasses[$className] ) ) {
			$filename = $globAutoloadClasses[$className];
		} else {
			return false;
		}

		# Make an absolute path, this improves performance by avoiding some stat calls
		if ( substr( $filename, 0, 1 ) != '/' && substr( $filename, 1, 1 ) != ':' ) {
			global $apiDir;
			$filename = "$apiDir/$filename";
		}

		require( $filename );

		return true;
	}

	/**
	 * Force a class to be run through the autoloader, helpful for things like
	 * Sanitizer that have define()s outside of their class definition. Of course
	 * this wouldn't be necessary if everything in MediaWiki was class-based. Sigh.
	 *
	 * @return Boolean Return the results of class_exists() so we know if we were successful
	 */
	static function loadClass( $class ) {
		return class_exists( $class );
	}
}

if ( function_exists( 'spl_autoload_register' ) ) {
	spl_autoload_register( array( 'AutoLoader', 'autoload' ) );
} else {
	function __autoload( $class ) {
		AutoLoader::autoload( $class );
	}

	ini_set( 'unserialize_callback_func', '__autoload' );
}
