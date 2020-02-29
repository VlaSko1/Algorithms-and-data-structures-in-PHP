<?php
//функция создает и возвращает массив из n элементов
const N = 10;
$n = N;
function getArray($n) {
    $arr = [];
    for ($i = 0; $i < $n; $i++) {
        $arr[$i] = $i + 1;
    }
    return $arr;
}

//функция удаляет один из элементов массива в случайным образом
function delOneElArray(&$arr) {
    $count = count($arr);
    $delElem = rand(0, $count - 1);
    array_splice($arr, $delElem, 1);
}


//функция возвращает пропущенные элемент в массиве, даже если отсутствует последний элемент массива.
function getMissingElemArr($arr) {
    $left = 0;
    $right =  count($arr) - 1;

    while ($left <= $right) {

        $middle = floor(($left + $right) / 2);
        echo "Индекс опорного элемента: {$middle}" . PHP_EOL;

        if ($arr[$middle] == $middle + 1) {
            if ($middle < $right && $arr[$middle + 1] != $middle + 2) return $middle + 2;
            if ($middle + 1 == count($arr) - 1) return $middle + 3;
            $left = $middle + 1;
        } elseif ($arr[$middle] != $middle + 1) {
            if ($middle == $left) return $middle + 1;
            if ($middle > $left && $arr[$middle - 1] == $middle) return $middle + 1;
            $right = $middle - 1;
        }
    }
    return null;
}
$array = getArray(N);
//var_dump($array);

delOneElArray($array);
var_dump($array);

echo "В массиве из {$n} элементов пропущен элемент со значением: " . getMissingElemArr($array);
