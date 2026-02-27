<?php

namespace Asinka;

use Asinka\Exceptions\UAGeneratorException;

/**
 * Class UAGenerator
 * @package Asinka
 */
class UAGenerator
{
	/** @var string */
	public const string OS_WINDOWS = 'win';
	/** @var string */
	public const string OS_LINUX = 'lin';
	/** @var string */
	public const string OS_MAC = 'mac';
	/** @var string */
	public const string OS_ANDROID = 'android';
	/** @var string */
	public const string OS_IOS = 'ios';
	/** @var string */
	public const string BROWSER_CHROME = 'chrome';
	/** @var string */
	public const string BROWSER_EDGE = 'edge';
	/** @var string */
	public const string BROWSER_IEXPLORER = 'iexplorer';
	/** @var string */
	public const string BROWSER_FIREFOX = 'firefox';
	/** @var string */
	public const string BROWSER_SAFARI = 'safari';
	/** @var string */
	public const string BROWSER_OPERA = 'opera';

	/**
	 * @return string[]
	 * @throws UAGeneratorException
	 */
	private static function chooseRandomBrowserAndOS(): array
	{
		$frequencies = [
			58 => [
				55 => [self::BROWSER_CHROME, self::OS_WINDOWS],
				15 => [self::BROWSER_CHROME, self::OS_MAC],
				8  => [self::BROWSER_CHROME, self::OS_LINUX],
				18 => [self::BROWSER_CHROME, self::OS_ANDROID],
				4  => [self::BROWSER_CHROME, self::OS_IOS],
			],
			20 => [
				95 => [self::BROWSER_EDGE, self::OS_WINDOWS],
				5  => [self::BROWSER_EDGE, self::OS_MAC],
			],
			10 => [
				65 => [self::BROWSER_SAFARI, self::OS_MAC],
				35 => [self::BROWSER_SAFARI, self::OS_IOS],
			],
			9  => [
				50 => [self::BROWSER_FIREFOX, self::OS_WINDOWS],
				20 => [self::BROWSER_FIREFOX, self::OS_MAC],
				20 => [self::BROWSER_FIREFOX, self::OS_LINUX],
				10 => [self::BROWSER_FIREFOX, self::OS_ANDROID],
			],
			3  => [
				60 => [self::BROWSER_OPERA, self::OS_WINDOWS],
				20 => [self::BROWSER_OPERA, self::OS_MAC],
				10 => [self::BROWSER_OPERA, self::OS_LINUX],
				10 => [self::BROWSER_OPERA, self::OS_ANDROID],
			],
		];
		$rand        = UAHelper::randomInt(1, 100);
		$sum         = 0;
		foreach ($frequencies as $freq => $osFreqs) {
			$sum += $freq;
			if ($rand <= $sum) {
				$rand = UAHelper::randomInt(1, 100);
				$sum  = 0;
				foreach ($osFreqs as $freq2 => $choice) {
					$sum += $freq2;
					if ($rand <= $sum) {
						return $choice;
					}
				}
			}
		}
		throw new UAGeneratorException("Frequencies don't sum to 100.");
	}

	/*** @return string */
	private static function windowsPlatform(): string
	{
		return UAHelper::arrayRandom([
			'Windows NT 10.0; Win64; x64',
			'Windows NT 10.0; WOW64',
		]);
	}

	/*** @return string */
	private static function osxVersion(): string
	{
		$major = UAHelper::arrayRandom([13, 14, 15]);
		$minor = UAHelper::randomInt(0, 6);
		return $major . '_' . $minor;
	}

	/*** @return int */
	private static function androidVersion(): int
	{
		return UAHelper::randomInt(13, 15);
	}

	/*** @return string */
	private static function iosVersion(): string
	{
		$major = UAHelper::randomInt(17, 18);
		$minor = UAHelper::randomInt(0, 6);
		return $major . '_' . $minor;
	}

	/*** @return string */
	private static function chromeVersion(): string
	{
		return UAHelper::randomInt(132, 147) . '.0.' . UAHelper::randomInt(6700, 7099) . '.' . UAHelper::randomInt(0, 199);
	}

	/*** @return string */
	private static function edgeVersion(): string
	{
		return UAHelper::randomInt(132, 136) . '.0.' . UAHelper::randomInt(3000, 3499) . '.' . UAHelper::randomInt(0, 99);
	}

	/*** @return string */
	private static function firefoxVersion(): string
	{
		return UAHelper::randomInt(133, 136) . '.0';
	}

	/*** @return string */
	private static function safariVersion(): string
	{
		return UAHelper::arrayRandom([
			'17.' . UAHelper::randomInt(0, 6),
			'18.' . UAHelper::randomInt(0, 2),
		]);
	}

	/*** @return string */
	private static function operaVersion(): string
	{
		return UAHelper::randomInt(112, 118) . '.0.' . UAHelper::randomInt(0, 99) . '.' . UAHelper::randomInt(0, 99);
	}

	/**
	 * @param string $arch
	 * @return string
	 */
	private static function firefox(string $arch = self::OS_WINDOWS): string
	{
		$version = self::firefoxVersion();
		switch ($arch) {
			case self::OS_ANDROID:
				$android = self::androidVersion();
				return "(Android $android; Mobile; rv:$version) Gecko/20100101 Firefox/$version";
			case self::OS_LINUX:
				return "(X11; Linux x86_64; rv:$version) Gecko/20100101 Firefox/$version";
			case self::OS_MAC:
				$osx = self::osxVersion();
				return "(Macintosh; Intel Mac OS X $osx; rv:$version) Gecko/20100101 Firefox/$version";
			case self::OS_WINDOWS:
			default:
				$platform = self::windowsPlatform();
				return "($platform; rv:$version) Gecko/20100101 Firefox/$version";
		}
	}

	/**
	 * @param string $arch
	 * @return string
	 */
	private static function safari(string $arch = self::OS_MAC): string
	{
		$webkit  = '605.1.15';
		$version = self::safariVersion();
		return match ($arch) {
			self::OS_IOS => "(iPhone; CPU iPhone OS " . self::iosVersion() . " like Mac OS X) AppleWebKit/$webkit (KHTML, like Gecko) Version/$version Mobile/15E148 Safari/604.1",
			default => "(Macintosh; Intel Mac OS X " . self::osxVersion() . ") AppleWebKit/$webkit (KHTML, like Gecko) Version/$version Safari/$webkit",
		};
	}

	/**
	 * @param string $arch
	 * @return string
	 */
	private static function edge(string $arch = self::OS_WINDOWS): string
	{
		$chrome = self::chromeVersion();
		$edge   = self::edgeVersion();
		switch ($arch) {
			case self::OS_LINUX:
				return "(X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Safari/537.36 Edg/$edge";
			case self::OS_MAC:
				$osx = self::osxVersion();
				return "(Macintosh; Intel Mac OS X $osx) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Safari/537.36 Edg/$edge";
			case self::OS_WINDOWS:
			default:
				$platform = self::windowsPlatform();
				return "($platform) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Safari/537.36 Edg/$edge";
		}
	}

	/**
	 * @param string $arch
	 * @return string
	 */
	private static function opera(string $arch = self::OS_WINDOWS): string
	{
		$chrome  = self::chromeVersion();
		$version = self::operaVersion();
		switch ($arch) {
			case self::OS_ANDROID:
				$android = self::androidVersion();
				return "(Linux; Android $android; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Mobile Safari/537.36 OPR/$version";
			case self::OS_LINUX:
				return "(X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Safari/537.36 OPR/$version";
			case self::OS_MAC:
				$osx = self::osxVersion();
				return "(Macintosh; Intel Mac OS X $osx) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Safari/537.36 OPR/$version";
			case self::OS_WINDOWS:
			default:
				$platform = self::windowsPlatform();
				return "($platform) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chrome Safari/537.36 OPR/$version";
		}
	}

	/**
	 * @param string $arch
	 * @return string
	 */
	private static function chrome(string $arch = self::OS_WINDOWS): string
	{
		$webkit = '537.36';
		$chrome = self::chromeVersion();
		switch ($arch) {
			case self::OS_ANDROID:
				$android = self::androidVersion();
				return "(Linux; Android $android; Pixel 7) AppleWebKit/$webkit (KHTML, like Gecko) Chrome/$chrome Mobile Safari/$webkit";
			case self::OS_IOS:
				$webkitIos = '605.1.15';
				return "(iPhone; CPU iPhone OS " . self::iosVersion() . " like Mac OS X) AppleWebKit/$webkitIos (KHTML, like Gecko) CriOS/$chrome Mobile/15E148 Safari/604.1";
			case self::OS_LINUX:
				return "(X11; Linux x86_64) AppleWebKit/$webkit (KHTML, like Gecko) Chrome/$chrome Safari/$webkit";
			case self::OS_MAC:
				$osx = self::osxVersion();
				return "(Macintosh; Intel Mac OS X $osx) AppleWebKit/$webkit (KHTML, like Gecko) Chrome/$chrome Safari/$webkit";
			case self::OS_WINDOWS:
			default:
				$platform = self::windowsPlatform();
				return "($platform) AppleWebKit/$webkit (KHTML, like Gecko) Chrome/$chrome Safari/$webkit";
		}
	}

	/**
	 * Main function which will choose random browser
	 * @param string|NULL $browser
	 * @param string|NULL $os
	 * @return string       user agent
	 * @throws UAGeneratorException
	 */
	public static function randomAgent(?string $browser = NULL, ?string $os = NULL): string
	{
		[$genBrowser, $genOs] = self::chooseRandomBrowserAndOs();
		$browser = $browser ?? $genBrowser;
		$os      = $os ?? $genOs;
		// Keep backward compatibility for explicit IE requests.
		if ($browser === self::BROWSER_IEXPLORER) {
			$browser = self::BROWSER_EDGE;
			$os      = self::OS_WINDOWS;
		}
		if ($browser === self::BROWSER_SAFARI) {
			$os = in_array($os, [self::OS_MAC, self::OS_IOS], TRUE) ? $os : self::OS_MAC;
		}
		if ($browser === self::BROWSER_FIREFOX && $os === self::OS_IOS) {
			throw new UAGeneratorException('Firefox on iOS is not supported.');
		}
		return match ($browser) {
			self::BROWSER_FIREFOX => "Mozilla/5.0 " . self::firefox($os),
			self::BROWSER_SAFARI => "Mozilla/5.0 " . self::safari($os),
			self::BROWSER_EDGE => "Mozilla/5.0 " . self::edge($os),
			self::BROWSER_IEXPLORER => "Mozilla/5.0 " . self::edge(self::OS_WINDOWS),
			self::BROWSER_OPERA => "Mozilla/5.0 " . self::opera($os),
			self::BROWSER_CHROME => 'Mozilla/5.0 ' . self::chrome($os),
			default => throw new UAGeneratorException('Unknown browser: ' . $browser),
		};
	}
}