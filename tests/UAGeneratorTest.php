<?php

declare(strict_types=1);

namespace Asinka\Tests;

use Asinka\Exceptions\UAGeneratorException;
use Asinka\UAGenerator;
use PHPUnit\Framework\TestCase;

final class UAGeneratorTest extends TestCase
{
	public function testChromeAndroidReturnsMobileUa(): void
	{
		$ua = UAGenerator::randomAgent(
			UAGenerator::BROWSER_CHROME,
			UAGenerator::OS_ANDROID
		);

		self::assertStringContainsString('Android', $ua);
		self::assertStringContainsString('Mobile', $ua);
		self::assertMatchesRegularExpression('/Chrome\/\d+\.\d+\.\d+\.\d+/', $ua);
	}

	public function testSafariIosReturnsIosUa(): void
	{
		$ua = UAGenerator::randomAgent(
			UAGenerator::BROWSER_SAFARI,
			UAGenerator::OS_IOS
		);

		self::assertStringContainsString('iPhone; CPU iPhone OS', $ua);
		self::assertStringContainsString('Mobile/15E148', $ua);
		self::assertStringContainsString('Safari/604.1', $ua);
	}

	public function testFirefoxIosThrowsException(): void
	{
		$this->expectException(UAGeneratorException::class);
		$this->expectExceptionMessage('Firefox on iOS is not supported.');

		UAGenerator::randomAgent(
			UAGenerator::BROWSER_FIREFOX,
			UAGenerator::OS_IOS
		);
	}

	public function testChromeWindowsDoesNotContainMobileMarker(): void
	{
		$ua = UAGenerator::randomAgent(
			UAGenerator::BROWSER_CHROME,
			UAGenerator::OS_WINDOWS
		);

		self::assertStringContainsString('Windows NT 10.0', $ua);
		self::assertStringNotContainsString(' Mobile ', $ua);
	}

	public function testRandomAgentWithoutArgumentsReturnsUaString(): void
	{
		$ua = UAGenerator::randomAgent();

		self::assertNotSame('', trim($ua));
		self::assertStringStartsWith('Mozilla/5.0 ', $ua);
	}
}
