<?php

// CLI check
if (php_sapi_name() != "cli") {
    die("This script is for CLI only");
}

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

// Url candidate setting
$url = "http://httpbin.org/image";

$httpClient = new Client();
for ($i=0; $i < 10; $i++) { 
    $requests[] = new Request('GET', $url, []);
}

// Time log start
$time['start'] = microtime(true);

// Guzzle Pool Batch
$responses = Pool::batch($httpClient, $requests, [
    'concurrency' => 10,
    'fulfilled' => function($response, $index) {
        // Nothing
    }, 
    'options' => ['on_stats' => function(TransferStats $stats) {

        // var_dump($stats->getHandlerStats());

        $statusCode = ($stats->hasResponse()) ? $stats->getResponse()->getStatusCode() : "Failed";

        // Get time
        list($usec, $unixtime) = explode(" ", microtime());
        $endTime = date("Y-m-d H:i:s." . $usec, $unixtime);
        $apiTime = $stats->getTransferTime();

        // Output
        echo "Received Time: {$endTime} | StatusCode: {$statusCode} | Transfer: {$apiTime}s\n";
    }
]]);

// Time log end
$time['end'] = microtime(true);
$time['seconds'] = $time['end'] - $time['start'];
echo "Period: {$time['seconds']}s \n";