<?php

declare(strict_types=1);

namespace Asinka\Tests;

use Asinka\Headers\BrowserFamily;
use Asinka\Headers\DeviceType;
use Asinka\Headers\DocumentHeadersGenerator;
use Asinka\Headers\Platform;
use Asinka\Headers\UaGeneratorInterface;
use Asinka\Headers\UaProfile;
use PHPUnit\Framework\TestCase;

final class DocumentHeadersGeneratorTest extends TestCase
{
	public function testChromiumWindowsDesktopIncludesClientHintsAndNavigationHeaders(): void
	{
		$generator = new DocumentHeadersGenerator(new FakeUaGenerator(
			new UaProfile(
				userAgent: 'Mozilla/5.0 (...) Chrome/136.0.7000.42 Safari/537.36',
				browserFamily: BrowserFamily::CHROMIUM,
				platform: Platform::WINDOWS,
				device: DeviceType::DESKTOP,
				majorVersion: 136,
			)
		));

		$headers = $generator->generateHeaders('chromium', 'Windows');

		self::assertArrayHasKey('Sec-CH-UA', $headers);
		self::assertArrayHasKey('Sec-CH-UA-Mobile', $headers);
		self::assertArrayHasKey('Sec-CH-UA-Platform', $headers);
		self::assertArrayHasKey('Sec-Fetch-Site', $headers);
		self::assertArrayHasKey('Sec-Fetch-Mode', $headers);
		self::assertArrayHasKey('Sec-Fetch-Dest', $headers);
		self::assertArrayHasKey('Sec-Fetch-User', $headers);
		self::assertArrayHasKey('Upgrade-Insecure-Requests', $headers);
		self::assertSame('?0', $headers['Sec-CH-UA-Mobile']);
		self::assertSame('"Windows"', $headers['Sec-CH-UA-Platform']);
	}

	public function testFirefoxLinuxDesktopDoesNotIncludeClientHints(): void
	{
		$generator = new DocumentHeadersGenerator(new FakeUaGenerator(
			new UaProfile(
				userAgent: 'Mozilla/5.0 (X11; Linux x86_64; rv:135.0) Gecko/20100101 Firefox/135.0',
				browserFamily: BrowserFamily::FIREFOX,
				platform: Platform::LINUX,
				device: DeviceType::DESKTOP,
			)
		));

		$headers = $generator->generateHeaders('firefox', 'Linux');

		self::assertArrayNotHasKey('Sec-CH-UA', $headers);
		self::assertArrayNotHasKey('Sec-CH-UA-Mobile', $headers);
		self::assertArrayNotHasKey('Sec-CH-UA-Platform', $headers);
		self::assertArrayHasKey('Accept', $headers);
		self::assertArrayHasKey('User-Agent', $headers);
	}

	public function testSafariIosMobileHasBaseHeadersWithoutClientHints(): void
	{
		$generator = new DocumentHeadersGenerator(new FakeUaGenerator(
			new UaProfile(
				userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.2 Mobile/15E148 Safari/604.1',
				browserFamily: BrowserFamily::SAFARI,
				platform: Platform::IOS,
				device: DeviceType::MOBILE,
			)
		));

		$headers = $generator->generateHeaders('safari', 'iOS');

		self::assertArrayNotHasKey('Sec-CH-UA', $headers);
		self::assertArrayNotHasKey('Sec-CH-UA-Mobile', $headers);
		self::assertArrayNotHasKey('Sec-CH-UA-Platform', $headers);
		self::assertSame('gzip, deflate, br', $headers['Accept-Encoding']);
		self::assertSame('keep-alive', $headers['Connection']);
	}

	public function testChromiumAndroidMobileSetsMobileHintAndAndroidPlatform(): void
	{
		$generator = new DocumentHeadersGenerator(new FakeUaGenerator(
			new UaProfile(
				userAgent: 'Mozilla/5.0 (Linux; Android 14; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.7012.99 Mobile Safari/537.36',
				browserFamily: BrowserFamily::CHROMIUM,
				platform: Platform::ANDROID,
				device: DeviceType::MOBILE,
				majorVersion: 135,
			)
		));

		$headers = $generator->generateHeaders('chromium', 'Android');

		self::assertSame('?1', $headers['Sec-CH-UA-Mobile']);
		self::assertSame('"Android"', $headers['Sec-CH-UA-Platform']);
	}
}

final readonly class FakeUaGenerator implements UaGeneratorInterface
{
	public function __construct(private UaProfile $profile)
	{
	}

	public function generate(?string $browserFamily = null, ?string $platform = null): UaProfile
	{
		return $this->profile;
	}
}
