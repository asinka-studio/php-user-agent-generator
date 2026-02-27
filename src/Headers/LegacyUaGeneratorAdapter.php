<?php

namespace Asinka\Headers;

use Asinka\Exceptions\UAGeneratorException;
use Asinka\UAGenerator;
use Asinka\UAHelper;

/**
 * Class LegacyUaGeneratorAdapter
 * @package Asinka\Headers
 */
final readonly class LegacyUaGeneratorAdapter implements UaGeneratorInterface
{

	/**
	 * @param string|null $browserFamily
	 * @param string|null $platform
	 * @return UaProfile
	 * @throws UAGeneratorException
	 */
	public function generate(?string $browserFamily = NULL, ?string $platform = NULL): UaProfile
	{
		$family         = $this->resolveFamily($browserFamily);
		$targetPlatform = $this->resolvePlatform($platform);
		if ($family === NULL || $targetPlatform === NULL) {
			[$family, $targetPlatform] = $this->randomFamilyAndPlatform();
		}
		$this->assertSupportedCombination($family, $targetPlatform);
		$device       = $this->deviceFromPlatform($targetPlatform);
		$userAgent    = $this->buildUa($family, $targetPlatform);
		$majorVersion = $family === BrowserFamily::CHROMIUM
			? $this->extractChromiumMajor($userAgent)
			: NULL;
		return new UaProfile(
			userAgent: $userAgent,
			browserFamily: $family,
			platform: $targetPlatform,
			device: $device,
			majorVersion: $majorVersion,
		);
	}

	/**
	 * @param string|null $browserFamily
	 * @return BrowserFamily|null
	 * @throws UAGeneratorException
	 */
	private function resolveFamily(?string $browserFamily): ?BrowserFamily
	{
		if ($browserFamily === NULL) {
			return NULL;
		}
		$normalized = strtolower(trim($browserFamily));
		return BrowserFamily::tryFrom($normalized)
			?? throw new UAGeneratorException('Unsupported browser family: ' . $browserFamily);
	}

	/**
	 * @param string|null $platform
	 * @return Platform|null
	 * @throws UAGeneratorException
	 */
	private function resolvePlatform(?string $platform): ?Platform
	{
		if ($platform === NULL) {
			return NULL;
		}
		$normalized = strtolower(trim($platform));
		return match ($normalized) {
			'windows' => Platform::WINDOWS,
			'macos' => Platform::MACOS,
			'linux' => Platform::LINUX,
			'android' => Platform::ANDROID,
			'ios' => Platform::IOS,
			default => throw new UAGeneratorException('Unsupported platform: ' . $platform),
		};
	}

	/**
	 * @return array{BrowserFamily, Platform}
	 */
	private function randomFamilyAndPlatform(): array
	{
		$family   = match (UAHelper::randomInt(1, 100)) {
			1, 2, 3, 4, 5, 6, 7, 8, 9, 10 => BrowserFamily::SAFARI,
			11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25 => BrowserFamily::FIREFOX,
			default => BrowserFamily::CHROMIUM,
		};
		$platform = match ($family) {
			BrowserFamily::SAFARI => UAHelper::randomInt(1, 100) <= 70 ? Platform::MACOS : Platform::IOS,
			BrowserFamily::FIREFOX => match (UAHelper::randomInt(1, 100)) {
				1, 2, 3, 4, 5, 6, 7, 8, 9, 10 => Platform::ANDROID,
				11, 12, 13, 14, 15 => Platform::MACOS,
				16, 17, 18, 19, 20 => Platform::LINUX,
				default => Platform::WINDOWS,
			},
			BrowserFamily::CHROMIUM => match (UAHelper::randomInt(1, 100)) {
				1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => Platform::ANDROID,
				13, 14, 15, 16, 17, 18 => Platform::MACOS,
				19, 20, 21, 22 => Platform::LINUX,
				23 => Platform::IOS,
				default => Platform::WINDOWS,
			},
		};
		return [$family, $platform];
	}

	/**
	 * @param BrowserFamily $browserFamily
	 * @param Platform      $platform
	 * @throws UAGeneratorException
	 */
	private function assertSupportedCombination(BrowserFamily $browserFamily, Platform $platform): void
	{
		if ($browserFamily === BrowserFamily::SAFARI && ! in_array($platform, [Platform::MACOS, Platform::IOS], TRUE)) {
			throw new UAGeneratorException('Safari is supported only on macOS and iOS.');
		}
		if ($browserFamily === BrowserFamily::FIREFOX && $platform === Platform::IOS) {
			throw new UAGeneratorException('Firefox profile for iOS is not supported by this adapter.');
		}
	}

	/**
	 * @param Platform $platform
	 * @return DeviceType
	 */
	private function deviceFromPlatform(Platform $platform): DeviceType
	{
		return match ($platform) {
			Platform::ANDROID, Platform::IOS => DeviceType::MOBILE,
			default => DeviceType::DESKTOP,
		};
	}

	/**
	 * @param BrowserFamily $family
	 * @param Platform      $platform
	 * @return string
	 * @throws UAGeneratorException
	 */
	private function buildUa(BrowserFamily $family, Platform $platform): string
	{
		$legacyBrowser  = match ($family) {
			BrowserFamily::CHROMIUM => UAGenerator::BROWSER_CHROME,
			BrowserFamily::FIREFOX => UAGenerator::BROWSER_FIREFOX,
			BrowserFamily::SAFARI => UAGenerator::BROWSER_SAFARI,
		};
		$legacyPlatform = match ($platform) {
			Platform::WINDOWS => UAGenerator::OS_WINDOWS,
			Platform::MACOS => UAGenerator::OS_MAC,
			Platform::LINUX => UAGenerator::OS_LINUX,
			Platform::ANDROID => UAGenerator::OS_ANDROID,
			Platform::IOS => UAGenerator::OS_IOS,
		};
		return UAGenerator::randomAgent($legacyBrowser, $legacyPlatform);
	}

	/**
	 * @param string $userAgent
	 * @return int|null
	 */
	private function extractChromiumMajor(string $userAgent): ?int
	{
		if (preg_match('/(?:Chrome|CriOS|Edg|OPR)\/(\d{2,3})/', $userAgent, $matches) !== 1) {
			return NULL;
		}
		return (int) $matches[1];
	}
}
