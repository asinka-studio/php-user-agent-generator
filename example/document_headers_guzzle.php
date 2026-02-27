<?php

declare(strict_types=1);

use Asinka\Headers\BrowserFamily;
use Asinka\Headers\DocumentHeadersGenerator;
use Asinka\Headers\Platform;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$headersGenerator = new DocumentHeadersGenerator();

// 1) Fully random browser/platform profile
$headersRandom = $headersGenerator->generateHeaders();
echo "Random document headers:" . PHP_EOL;
print_r($headersRandom);
echo PHP_EOL;

// 2) Explicit browser family + platform
$headersChromiumWindows = $headersGenerator->generateHeaders(BrowserFamily::CHROMIUM->value, Platform::WINDOWS->value);
echo "Chromium/Windows document headers:" . PHP_EOL;
print_r($headersChromiumWindows);
echo PHP_EOL;

// Optional HTTP request with Guzzle.
if (!class_exists(Client::class)) {
	echo "Install guzzlehttp/guzzle to run the HTTP request part of this example." . PHP_EOL;
	exit(0);
}

$client = new Client();
$response = $client->request('GET', 'https://example.com', [
	'headers' => $headersChromiumWindows,
]);

echo "Response status: " . $response->getStatusCode() . PHP_EOL;
