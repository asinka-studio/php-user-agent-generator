<?php

namespace Asinka\Headers;

/**
 * Class UaProfile
 * @package Asinka\Headers
 */
final readonly class UaProfile
{
	/**
	 * @param string        $userAgent
	 * @param BrowserFamily $browserFamily
	 * @param Platform      $platform
	 * @param DeviceType    $device
	 * @param int|null      $majorVersion
	 * @param string|null   $locale
	 */
	public function __construct(
		public string        $userAgent,
		public BrowserFamily $browserFamily,
		public Platform      $platform,
		public DeviceType    $device,
		public ?int          $majorVersion = NULL,
		public ?string       $locale = NULL,
	) {
	}
}
