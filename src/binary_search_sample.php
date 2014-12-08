<?php

$target = array('db1', 'db2', 'db3');
$weight = array(50, 25, 25);

echo getValueByWeight($target, $weight) . "\n";

/**
 * 値の配列とその重み付けの組み合わせから、ランダムでひとつの値を選ぶ
 * 
 * @param array $target 値の配列
 * @param array $weight $targetに対する重み付け
 * 
 * @return string 重み付けに従った確率で選ばれた値
 */
function getValueByWeight($target, $weight) {
    $total_weight = 0;
    $weight_lookup = array();
    for ($count = 0; $count < count($weight); $count++) {
        $total_weight += $weight[$count];
        $weight_lookup[] = $total_weight; 
    }
    $rand = mt_rand(0, $total_weight);
    list($key, $low, $high) = binarysearch($rand, $weight_lookup);
    if ($key === null) {   
        if ($weight_lookup[$low] >= $rand) {
            $key = $low;
        }
        else {
            $key = $low + 1;
        }
    }
    return $target[$key];    
}

/**
 * 二分探索を実行する
 *
 * @param int $needle 探す対象の値
 * @param array $haystack 探索範囲
 *
 * @return array(int, int, int) $needleに対するキー、探索終了時のlow、探索終了時のhigh
 */
function binarysearch($needle, array $haystack) {
    $high = count($haystack) - 1;
    $low = 0;
    while ($low < $high) {
        $medium = (int)(($low + $high) / 2);
        if ($haystack[$medium] === $needle) {
            return array($medium, $low, $high);
        }
        if ($haystack[$medium] < $needle) {
            $low = $medium + 1;
        }
        else {
            $high = $medium - 1;
        }
    }
    return array(null, $low, $high);
}
