<?php

namespace EddConvertkitStubs\Tests;

use PHPUnit\Framework\TestCase;

class StubSyntaxTest extends TestCase {

	private string $stubsFile;

	protected function setUp(): void {
		$this->stubsFile = __DIR__ . '/../edd-convertkit-stubs.php';
	}

	public function testStubFileExists(): void {
		$this->assertFileExists( $this->stubsFile, 'Stub file should exist' );
	}

	public function testStubFileIsReadable(): void {
		$this->assertFileIsReadable( $this->stubsFile, 'Stub file should be readable' );
	}

	public function testStubFileHasValidSyntax(): void {
		$output   = array();
		$exitCode = 0;
		exec( 'php -l ' . escapeshellarg( $this->stubsFile ) . ' 2>&1', $output, $exitCode );

		$this->assertEquals( 0, $exitCode, 'Stub file should have valid PHP syntax: ' . implode( "\n", $output ) );
	}

	public function testNoStrayCodeStatements(): void {
		$stubContent = file_get_contents( $this->stubsFile );
		$this->assertNotFalse( $stubContent, 'Stub file should be readable' );

		$this->assertDoesNotMatchRegularExpression(
			'/^\s*\$\w+\s*=.*\$this->/m',
			$stubContent,
			'Should not have stray $this references at namespace level'
		);

		$this->assertDoesNotMatchRegularExpression(
			'/^\s*\$\w+\s*=\s*apply_filters\(/m',
			$stubContent,
			'Should not have stray apply_filters calls at namespace level'
		);
	}

	public function testEddConvertkitVersionConstant(): void {
		$this->assertTrue( defined( 'EDD_CONVERTKIT_VERSION' ), 'EDD_CONVERTKIT_VERSION should be defined' );

		/** @var string $version */
		$version = EDD_CONVERTKIT_VERSION;

		$this->assertMatchesRegularExpression(
			'/^\d+\.\d+(\.\d+)?(\.\d+)?/',
			$version,
			'EDD_CONVERTKIT_VERSION should be in semantic version format'
		);
	}

	/**
	 * Core EDD ConvertKit symbols used by every consumer.
	 */
	public function testCoreClassExists(): void {
		$this->assertTrue(
			class_exists( 'EDD_ConvertKit' ),
			'EDD_ConvertKit class should exist'
		);
	}
}
