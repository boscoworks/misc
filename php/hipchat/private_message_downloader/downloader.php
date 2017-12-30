<?php

$conf = parse_ini_file(__DIR__ . '/downloader.conf');

// Initialize variables
$host = $conf['hipchat_host'];
$maxTrialNum = $conf['max_trial_num'];
$userId = $argv[1];
$flag = true;
$index = 0;
$trial = 0;
$result = $argv[4];

// Initialize HTTP query parameters
$queries = [];
$queries['auth_token'] = $conf['hipchat_api_token'];
$queries['timezone'] = 'Asia/Tokyo';
$queries['date'] = $argv[3]; // YYYY-mm-dd
$queries['enddate'] = $argv[2]; // YYYY-mm-dd
$queries['reverse'] = 'false';
$queries['start-index'] = $index;
$queries['max-results'] = $conf['max_results'];

while($flag) {
    if ($trial >= $maxTrialNum) {
        echo "max trial.\n";
        exit();
    }
    $queries['start-index'] = $index;
    $apiUrl = 'https://' . $host . '/v2/user/' . $userId . '/history?' . http_build_query($queries);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $ret = curl_exec($ch);
    if ($ret === false) { // when curl is failed, try to reconnect.
        $trial += 1;
        continue;
    }
    if (preg_match('/.*(\"items\": \[\])/', $ret)) { // when return value is empty, 'while()' flag updated.
        $flag = false;
    }
    $resources = json_decode($ret);
    $items = $resources->items;
    foreach ($items as $item) { // convert json to ltsv.
        $logs = [];
        $message = str_replace("\n", '^n', $item->message); // ltsv does not include 'new line'.
        $message = str_replace("\t", '^t', $message); // ltsv does not include 'tab'.
        $logs[] = "date:\t" . $item->date;
        $logs[] = "from:\t" . $item->from->mention_name . ' - ' . $item->from->name;
        $logs[] = "message:\t" . $message;
        $log = implode("\t", $logs) . "\n";
        file_put_contents($result, $log, FILE_APPEND | LOCK_EX);
    }
    echo "Index: $index done.\n";
    $index += $queries['max-results'];
    curl_close($ch);
    sleep(1);
}
echo "done.\n";

