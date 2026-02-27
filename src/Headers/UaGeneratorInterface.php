<?php

namespace Asinka\Headers;

/*** Interface UaGeneratorInterface */
interface UaGeneratorInterface
{
	/**
	 * @param string|null $browserFamily
	 * @param string|null $platform
	 * @return UaProfile
	 */
	public function generate(?string $browserFamily = NULL, ?string $platform = NULL): UaProfile;
}
