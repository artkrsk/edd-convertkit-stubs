#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use StubsGenerator\{StubsGenerator, Finder};
use Dotenv\Dotenv;

// Helper function for colored output
function color( string $text, string $color ): string {
	$colors = array(
		'green'  => "\033[32m",
		'red'    => "\033[31m",
		'yellow' => "\033[33m",
		'reset'  => "\033[0m",
	);

	return ( $colors[ $color ] ?? '' ) . $text . $colors['reset'];
}

// Extract the Version: header from EDD ConvertKit's main plugin file.
function extractConvertkitVersion( string $eddConvertkitPath ): string {
	$mainFile = $eddConvertkitPath . '/edd-convertkit.php';

	if ( ! file_exists( $mainFile ) ) {
		throw new \Exception( "EDD ConvertKit main file not found: {$mainFile}" );
	}

	$content = file_get_contents( $mainFile );

	if ( preg_match( '/^\s*\*\s*Version:\s*(.+)$/m', $content, $matches ) ) {
		return trim( $matches[1] );
	}

	throw new \Exception( "Could not extract version from {$mainFile}" );
}

// Load .env configuration (optional - CI sets env vars directly)
$dotenv = Dotenv::createImmutable( __DIR__ );
$dotenv->safeLoad();

$envEddConvertkitPath = getenv( 'EDD_CONVERTKIT_PATH' );
$eddConvertkitPath    = $envEddConvertkitPath ? $envEddConvertkitPath : ( $_ENV['EDD_CONVERTKIT_PATH'] ?? null );

if ( empty( $eddConvertkitPath ) ) {
	echo color( "Error: EDD_CONVERTKIT_PATH environment variable is required.\n", 'red' );
	echo "Please create .env file or set the environment variable.\n";
	echo "See .env.example for template.\n";
	exit( 1 );
}

if ( ! is_dir( $eddConvertkitPath ) ) {
	echo color( "Error: EDD ConvertKit source not found at $eddConvertkitPath\n", 'red' );
	echo "Please update EDD_CONVERTKIT_PATH in .env file.\n";
	exit( 1 );
}

echo color( "Generating stubs from: $eddConvertkitPath\n", 'yellow' );

// 1. Generate stubs
$finder = Finder::create()
	->in( $eddConvertkitPath )
	->exclude( array( 'vendor', 'tests', 'node_modules', 'build', 'assets', 'languages', 'libraries', 'samples', 'templates', 'views' ) )
	->sortByName();

$generator = new StubsGenerator( StubsGenerator::DEFAULT );
$result    = $generator->generate( $finder );
$content   = $result->prettyPrint();

// 2. Remove stray code statements (code outside functions/classes)
$content = removeStrayCodeStatements( $content );

// 2.5. Strip `abstract` from method declarations.
$content = neutralizeAbstractMethods( $content );

// 3. Extract version from source
$convertkitVersion = extractConvertkitVersion( $eddConvertkitPath );

// 4. Add self-contained constants with extracted version
$content = addSelfContainedConstants( $content, $convertkitVersion );

// 4.5. Add empty stubs for parent classes / interfaces / traits referenced from the
// stub file but missing from the scan.
$content = fixMissingTypeStubs( $content );

// 5. Write final output
file_put_contents( __DIR__ . '/edd-convertkit-stubs.php', $content );

echo color( "✓ Stubs generated successfully\n", 'green' );
echo color( "  EDD ConvertKit: $convertkitVersion\n", 'green' );

// ---------------------------------------------------------------------------
// Helper functions
// ---------------------------------------------------------------------------

/**
 * Iteratively detect classes / interfaces / traits the stub file references but doesn't
 * define, and append empty stubs for them so the file can be `require_once`'d without
 * triggering "Class X not found" fatals.
 *
 * The preload chain loads the EDD core stubs alongside WordPress / WP-CLI stubs so any
 * EDD core type ConvertKit references resolves cleanly.
 */
function fixMissingTypeStubs( string $content ): string {
	$max_passes = 30;

	for ( $pass = 0; $pass < $max_passes; $pass++ ) {
		$tmp = tempnam( sys_get_temp_dir(), 'edd_convertkit_stubs_' );
		file_put_contents( $tmp, $content );

		$preload = array(
			__DIR__ . '/vendor/autoload.php',
			__DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php',
			__DIR__ . '/vendor/php-stubs/wp-cli-stubs/wp-cli-stubs.php',
			__DIR__ . '/vendor/arts/easy-digital-downloads-stubs/easy-digital-downloads-stubs.php',
		);

		$requires = '';
		foreach ( $preload as $stub ) {
			if ( file_exists( $stub ) ) {
				$requires .= sprintf( 'require_once %s; ', var_export( $stub, true ) );
			}
		}
		$check_script = $requires . sprintf( 'require_once %s;', var_export( $tmp, true ) );

		$output_lines = array();
		exec(
			'php -d memory_limit=2G -r ' . escapeshellarg( $check_script ) . ' 2>&1',
			$output_lines,
			$exit_code
		);
		unlink( $tmp );

		$output = implode( "\n", $output_lines );

		$has_error = ( 0 !== $exit_code )
			|| ( false !== strpos( $output, 'Fatal error' ) )
			|| ( false !== strpos( $output, 'PHP Fatal' ) );

		if ( ! $has_error ) {
			break;
		}

		$changed = false;

		if ( preg_match_all( '/(Class|Interface|Trait) "([^"]+)" not found/', $output, $matches, PREG_SET_ORDER ) ) {
			$seen = array();
			foreach ( $matches as $match ) {
				$kind = strtolower( $match[1] );
				$fqcn = $match[2];
				$key  = $kind . ':' . $fqcn;
				if ( isset( $seen[ $key ] ) ) {
					continue;
				}
				$seen[ $key ] = true;

				$parts      = explode( '\\', ltrim( $fqcn, '\\' ) );
				$short_name = array_pop( $parts );
				$namespace  = implode( '\\', $parts );

				if ( '' === $namespace ) {
					$content .= "\nnamespace {\n\t{$kind} {$short_name} {}\n}\n";
				} else {
					$content .= "\nnamespace {$namespace} {\n\t{$kind} {$short_name} {}\n}\n";
				}
				$changed = true;
				echo color( "  → Added empty {$kind} stub for missing reference: {$fqcn}\n", 'yellow' );
			}
		}

		if ( ! $changed ) {
			echo color( "Warning: stub file still has parse errors after $pass passes:\n", 'yellow' );
			echo $output . "\n";
			break;
		}
	}

	return $content;
}

/**
 * Convert `abstract <visibility> function foo(...): T;` declarations into
 * `<visibility> function foo(...): T {}`. Preserves visibility, parameter list, and
 * return type so PHPStan's bootstrap of the stubs still sees a typed signature, but
 * removes the contract obligation PHP enforces at class-parse time.
 */
function neutralizeAbstractMethods( string $content ): string {
	return preg_replace_callback(
		'/abstract\s+((?:public|protected|private)(?:\s+static)?\s+function\s+\w+\s*\([^)]*\)(?:\s*:\s*[?\w\\\\|]+)?)\s*;/',
		function ( $matches ) {
			return $matches[1] . ' {}';
		},
		$content
	);
}

/**
 * Inject the EDD ConvertKit constant at the top of the stubs file so consumers don't
 * have to define it separately to satisfy `defined(...)` checks.
 *
 * Handles both cases:
 *   - Generated stub already uses braced namespaces (most StubsGenerator output) → prepend constants as a separate namespace block above the first `namespace ` line.
 *   - Generated stub is pure global-namespace classes (ConvertKit is small enough for this) → wrap the whole body in `namespace { ... }` so the constants block we add can coexist with it (PHP forbids mixing braced and unbraced syntax in one file).
 */
function addSelfContainedConstants( string $content, string $convertkitVersion ): string {
	// Drop any empty placeholder namespace blocks the generator may have emitted.
	$content = preg_replace( '/namespace \{\s*\}/', '', $content );
	// Normalize the file header so we can prepend cleanly.
	$content = preg_replace( '/^<\?php.*?\n/s', "<?php\n\n", $content );

	$constants = <<<CONSTANTS
namespace {
	// EDD ConvertKit constants
	if (!defined('EDD_CONVERTKIT_VERSION')) {
		define('EDD_CONVERTKIT_VERSION', '{$convertkitVersion}');
	}
}

CONSTANTS;

	if ( preg_match( '/^namespace /m', $content ) ) {
		// Mixed-namespace file — drop constants block right above the first namespace.
		return preg_replace( '/^(namespace )/m', $constants . '$1', $content, 1 );
	}

	// All-global-namespace file (e.g. EDD ConvertKit) — wrap the entire body in
	// `namespace { ... }` so the constants block we prepend can coexist with it.
	$body = preg_replace( '/^<\?php\s*\n/s', '', $content );
	return "<?php\n\n" . $constants . "namespace {\n" . $body . "\n}\n";
}

/**
 * Remove stray code statements that appear in namespace blocks outside of
 * class/function definitions.
 */
function removeStrayCodeStatements( string $content ): string {
	$lines  = explode( "\n", $content );
	$output = array();

	foreach ( $lines as $line ) {
		if ( preg_match( '/^\s*\$\w+\s*=.*\$this->/', $line ) ) {
			continue;
		}

		if ( preg_match( '/^\s*\$\w+\s*=\s*apply_filters\(/', $line ) ) {
			continue;
		}

		if ( preg_match( '/^\s*\\\\?define\s*\(/', $line ) ) {
			continue;
		}

		if ( preg_match( '/^\s{0,4}\$\w+\s*=/', $line ) ) {
			continue;
		}

		$output[] = $line;
	}

	$content = implode( "\n", $output );

	$content = preg_replace(
		'/namespace\s+[\w\\\\]+\s*\{\s*\/\*\*[^*]*\*+(?:[^*\/][^*]*\*+)*\/\s*\}/s',
		'',
		$content
	);

	$content = preg_replace( '/\n{3,}/', "\n\n", $content );

	return $content;
}
