<?php

namespace Asinka\Headers;

use Asinka\Exceptions\UAGeneratorException;

/**
 * Class DocumentHeadersGenerator
 * @package Asinka\Headers
 */
final class DocumentHeadersGenerator
{
	/** @var string */
	private const string DEFAULT_ACCEPT_LANGUAGE = 'en-US,en;q=0.9';
	/** @var string */
	private const string ACCEPT_ENCODING = 'gzip, deflate, br';
	/** @var string */
	private const string ACCEPT_DOCUMENT = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8';

	public function __construct(
		private readonly UaGeneratorInterface $uaGenerator = new LegacyUaGeneratorAdapter(),
	) {
	}

	/**
	 * Generate browser-like headers for top-level HTML document navigation.
	 * @return array<string, string>
	 * @throws UAGeneratorException
	 */
	public function generateHeaders(?string $browserFamily = NULL, ?string $platform = NULL): array
	{
		$profile = $this->uaGenerator->generate($browserFamily, $platform);
		$headers = [
			'User-Agent'                => $profile->userAgent,
			'Accept'                    => self::ACCEPT_DOCUMENT,
			'Accept-Language'           => $profile->locale ?? self::DEFAULT_ACCEPT_LANGUAGE,
			'Accept-Encoding'           => self::ACCEPT_ENCODING,
			'Connection'                => 'keep-alive',
			'Upgrade-Insecure-Requests' => '1',
			'Sec-Fetch-Site'            => 'none',
			'Sec-Fetch-Mode'            => 'navigate',
			'Sec-Fetch-Dest'            => 'document',
			'Sec-Fetch-User'            => '?1',
		];
		if ($profile->browserFamily === BrowserFamily::CHROMIUM) {
			if ($profile->majorVersion === NULL) {
				throw new UAGeneratorException('Chromium profile must provide majorVersion.');
			}
			$headers += [
				'Sec-CH-UA'          => $this->buildSecChUa($profile->majorVersion),
				'Sec-CH-UA-Mobile'   => $profile->device === DeviceType::MOBILE ? '?1' : '?0',
				'Sec-CH-UA-Platform' => '"' . $profile->platform->value . '"',
			];
		}
		return $headers;
	}

	/**
	 * @param int $majorVersion
	 * @return string
	 */
	private function buildSecChUa(int $majorVersion): string
	{
		return sprintf(
			'"Chromium";v="%d", "Google Chrome";v="%d", "Not/A)Brand";v="99"',
			$majorVersion,
			$majorVersion
		);
	}
}
