<?php

namespace Asinka\Headers;

/**
 * Class BrowserFamily
 * @package Asinka\Headers
 */
enum BrowserFamily: string
{
	case CHROMIUM = 'chromium';
	case FIREFOX = 'firefox';
	case SAFARI = 'safari';
}
