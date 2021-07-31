<?php declare(strict_types=1);

use Analyzer\Analyzer;
use Analyzer\DataSource;

require __DIR__ . '/vendor/autoload.php';

$user = (new Analyzer(new DataSource(__DIR__ . '/data/user-channel.txt')))->analyze();
$customer = (new Analyzer(new DataSource(__DIR__ . '/data/customer-channel.txt')))->analyze();

$user = (new Analyzer(new DataSource(__DIR__ . '/data/user-small.txt')))->analyze();
$customer = (new Analyzer(new DataSource(__DIR__ . '/data/customer-small.txt')))->analyze();

$callDuration = max($user->getLatestEntry(), $customer->getLatestEntry());

$result = [
    'call_duration'              => $callDuration,
    'user_talked'                => $user->getTotalDuration(),
    'cust_talked'                => $customer->getTotalDuration(),
    'longest_user_monologue'     => $user->getLongestChunk(),
    'longest_customer_monologue' => $customer->getLongestChunk(),
    'user_talk_percentage'       => round(($user->getTotalDuration() / $callDuration) * 100, 2),
    'user'                       => $user->getChunks(),
    'customer'                   => $customer->getChunks(),
];

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
