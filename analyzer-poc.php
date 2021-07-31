<?php declare(strict_types=1);
// phpcs:ignoreFile
// @codingStandardsIgnoreFile

/**
 * Simple NO-OOP solution, kinda POC
 */

/**
 * Split silence data to monologue chunks
 * @param array $data
 * @return float
 */
function splitChunks(array $data): array
{
    global $callDuration;

    $result = [];
    $talkBegin = 0;

    foreach ($data as $row) {
        if (preg_match('/silence_start: ([\d\.]+)/', $row, $matches)) {
            $result[] = [$talkBegin, (float)$matches[1]];
        } elseif (preg_match('/silence_end: ([\d\.]+)/', $row, $matches)) {
            $talkBegin = (float)$matches[1];
        }
        $callDuration = max($callDuration, (float)$matches[1]);
    }
    return $result;
}

/**
 * Get longest monologue from chunks
 * @param array $data
 * @return float
 */
function findLongestChunk(array $data): float
{
    $result = 0;

    foreach ($data as $chunk) {
        $result = max($result, $chunk[1] - $chunk[0]);
    }

    return $result;
}

/**
 * Get total duration of talking from chunks
 * @param array $data
 * @return float
 */
function getTotalDuration(array $data): float
{
    $result = 0;

    foreach ($data as $chunk) {
        $result += $chunk[1] - $chunk[0];
    }

    return $result;
}

/**
 * Action starts
 */
$callDuration = 0;

// Load data

$userData = file('./data/user-channel.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$custData = file('./data/customer-channel.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


// Split to inverted chunks

$userChunks = splitChunks($userData);
$custChunks = splitChunks($custData);

// Analize the chunks


$longestUserMonologue = findLongestChunk($userChunks);
$longestCustMonologue = findLongestChunk($custChunks);

$userTalked = getTotalDuration($userChunks);
$custTalked = getTotalDuration($custChunks);

$result = [
    // 'call_duration'              => $callDuration,
    // 'user_talked'                => $userTalked,
    // 'cust_talked'                => $custTalked,
    'longest_user_monologue'     => $longestUserMonologue,
    'longest_customer_monologue' => $longestCustMonologue,
    'user_talk_percentage'       => round(($userTalked / $callDuration) * 100, 2),
    'user'                       => $userChunks,
    'customer'                   => $custChunks,
];

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
