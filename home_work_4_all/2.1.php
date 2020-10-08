<?php
// Реализуем функцию для создания чисел с разрядами 1000 и более и записи его в файл. 
function getDigitNumber($n)
{
    $strNumber = '';
    $strNumber .= rand(1, 9);
    for ($i = 0; $i < $n - 1; $i++) {
        $strNumber .= rand(0, 9);
    }
    $strNumber .= "\n";

    $wf = fopen("chisla.txt", "a");

    fwrite($wf, $strNumber);
    fclose($wf);
    
}


getDigitNumber(1001);
getDigitNumber(1001);

sumBigNumberFile();

// Суммирует два числа в файле chisla.txt и вписывает ответ на третью строчку
function sumBigNumberFile()
{
    $frw = fopen("chisla.txt", "a+")  or die("Не удалось открыть файл");
    
    $number1 = fgets($frw);
    $number2 = fgets($frw);
    $numberRev1 = substr(strrev($number1), 1);
    $numberRev2 = substr(strrev($number2), 1);
    $maxLength = (strlen($numberRev1) >= strlen($numberRev2)) ? strlen($numberRev1) : strlen($numberRev2);
    $sum = '';
    $remainder = 0;

    for ($i = 0; $i < $maxLength; $i++) {
        $num1 = is_numeric($numberRev1[$i]) ? $numberRev1[$i] : 0;
        $num2 = is_numeric($numberRev2[$i]) ? $numberRev2[$i] : 0;
        $sumNumber = $num1 + $num2 + $remainder;
        if ($sumNumber > 9) {
            $sumNumber .= '';
            $remainder = $sumNumber[0];
            $sumNumber = $sumNumber[1];
        } else {
            $remainder = 0;
        }
        $sum = $sumNumber . $sum;
    }
    if ($remainder !== 0) {
        $sum = $remainder . $sum . "\n";
    } else {
        $sum = $sum . "\n";
    }
    echo "Первое число: " . $number1 . "<br>";
    echo "Второе число: " . $number2 . "<br>";
    echo "Их сумма: " . $sum . "<br>";
    
    
    fwrite($frw, $sum);
    fclose($frw);

}







?>