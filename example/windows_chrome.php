<?php

use Asinka\UAGenerator;

require __DIR__ . '/../vendor/autoload.php';
try {
	$count     = 10;
	echo 'UserAgents:' . PHP_EOL;
	for ($x = 0; $x <= $count; $x++) {
		echo UAGenerator::randomAgent(UAGenerator::BROWSER_CHROME, UAGenerator::OS_WINDOWS) . PHP_EOL;
	}
} catch (Exception $e) {
	echo 'Error: ' . PHP_EOL . $e->getMessage() . PHP_EOL;
}