<?php

namespace Asinka\Headers;

/**
 * Class Platform
 * @package Asinka\Headers
 */
enum Platform: string
{
	case WINDOWS = 'Windows';
	case MACOS = 'macOS';
	case LINUX = 'Linux';
	case ANDROID = 'Android';
	case IOS = 'iOS';
}
