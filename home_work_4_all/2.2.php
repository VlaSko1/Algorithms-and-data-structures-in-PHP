<?php


// Вычисляет для натуральных чисел "а" и "n", a^n, где  1 <= a <= 9, 1 <= n <= 7000; ответ выводится в файл otvet.txt
function superPow($a, $n)
{
    $result = $a . '';
    
    for ($i = 1; $i < $n; $i++) {
        $interResult = '';
        $remainder = 0;
        for ($j = strlen($result); $j > 0; $j--) {
            
            $temp = $result[$j - 1] * $a + $remainder;
            
            if ($temp > 9) {
                $temp .= '';
                $interResult = $temp[1] . $interResult;
                $remainder = $temp[0];
            } else {
                $interResult = $temp . $interResult;
                $remainder = 0;
            }
        }
        if ($remainder !== 0) {
            $interResult = $remainder . $interResult;
        }
        $result = $interResult;
    }

    $frw = fopen('otvet.txt', 'a');
    $data = "{$a}^{$n}:" . "\n" . $result . "\n";
    fwrite($frw, $data);
    fclose($frw);

}

echo superPow(9, 1000);

?>