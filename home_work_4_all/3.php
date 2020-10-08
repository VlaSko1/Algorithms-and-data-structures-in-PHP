<?php
$start = microtime(true);

// Решение выражения обратной польской нотацией.

$temp = new PolishNotationBack("(36-25)*89-(36+41)^3+45*((25^2)+9499)");

echo "Заданное выражение: " . $temp->expression . '<br>';
echo "Обратная польская запись: " . $temp->polishExpressionBack . '<br>';
echo "Решение выражения: " . $temp->result . "<br>";
$end = microtime(true);
$finish = $end - $start;
echo "Время выполнения: " . $finish;

class PolishNotationBack {
    public function __construct($expression)
    {
        
        $this->expression = $this->prepare($expression);
        $this->createPolishExpression($this->expression);
        $this->result = $this->calculationBackPolish($this->polishExpressionBack);

    }
    // Выражение записанное в обратной польской нотации
    public $polishExpressionBack = null;


    // текущая вложенность 
    public $countBrace = 0;

    // данные текущей точки перегиба 
    public $pointInflection = null;

    // Приоритет операторов
    public $prioritySing = [
        '^' => 3,
        '*' => 2,
        '/' => 2,
        '+' => 1,
        '-' => 1
    ];

    // Подготавливаем выражение (убираем пробелы), делаем проверки на правильность выражения
    public function prepare($expression)
    {
        $exp = str_replace(" ", "", $expression);
        
        if (preg_match('/^[\d\.\-\*\/\+\^()]+$/', $exp)) {
            $leftBrace = substr_count($exp, '(');
            $rightBrace = substr_count($exp, ')');
            if ($leftBrace !== $rightBrace) {
                var_dump("Количество открывающихся и закрывающихся круглых скобок не одинаково, исправьте входные данные!");
            }
        } else {
            var_dump("Введите правильное выражение, содержащее цифры, знаки /, +, -, *, ^, (, ) и точки.");
        }
        return $exp;
    }

    // Преобразуем выражение в обратную польскую запись
    public function createPolishExpression($val) 
    {
        $valPolishBack = array($val);
        $i = 0;
        
        while($i < count($valPolishBack)) {
            if ($this->checkOnlyNumberOrSign($valPolishBack[$i])) {
                $i++;
            } else {

                $this->getPointInflection($valPolishBack[$i]);
            
                if ($this->pointInflection['brace'] > 0) {
                    while ($this->pointInflection['brace'] !== 0) {
                        $valPolishBack[$i] = substr($valPolishBack[$i], 1, strlen($valPolishBack[$i]) - 2);
                        $this->getPointInflection($valPolishBack[$i]);
                    }
                }
                
                $signPolish = $this->pointInflection['sign'];
                $left = substr($valPolishBack[$i], 0, $this->pointInflection['ind']);
                $right = substr($valPolishBack[$i], $this->pointInflection['ind'] + 1);
                
                array_splice($valPolishBack, $i, 1, array($left, $right, $signPolish));   
    
                if ($this->checkOnlyNumberOrSign($valPolishBack[$i])) {
                    $i++;
                }
            }
        }
        $this->polishExpressionBack = implode(', ', $valPolishBack);
    }

    // Возвращает true если строка содержит только цифры или только арифметический знак, иначе - false
    public function checkOnlyNumberOrSign($str)
    {
        $numberOnly = '/^\d+$/';
        $singOnly = '/^[\+\-\*\/\^]$/';
        return (preg_match($numberOnly, $str) || preg_match($singOnly, $str));
    }
    
    // Находим точку перегиба выражения
    public function getPointInflection($val)
    {
        $this->resetInflectionPoint();
        
        for ($i = 0; $i < strlen($val); $i++) {
            if ($val[$i] === '(') {
                $this->countBrace += 1;
            }
            if ($val[$i] === ')') {
                $this->countBrace -= 1;
            }
      
            if (array_key_exists($val[$i], $this->prioritySing)) {
                if (is_null($this->pointInflection)) {
                    $this->setInflectionPoint($val, $i);
                }
                if ($this->pointInflection['brace'] > $this->countBrace) {
                    $this->setInflectionPoint($val, $i);
                }
                if ($this->pointInflection['brace'] === $this->countBrace && 
                    $this->getPriority($this->pointInflection['sign']) >= $this->getPriority($val[$i])) {
                        $this->setInflectionPoint($val, $i);
                }
            }
        }
    }

    // Обнуление данных точки перегиба 
    public function resetInflectionPoint() 
    {
        $this->pointInflection = null;
    }

     // Установка новой точки перегиба   
     public function setInflectionPoint($val, $i)
     {
         $this->resetInflectionPoint();
         $this->pointInflection = array('ind' => $i, 'sign' => $val[$i], 'brace' => $this->countBrace);
         
     }
     // Получение приоритета
     public function getPriority($item) 
     {
         return $this->prioritySing[$item];
     }

    // Вычисляем выражение из обратной польской записи
    public function calculationBackPolish($backPolish)
    {
        $arrBackPolish = explode(", ", $backPolish);
        while(count($arrBackPolish) > 1) {
            for ($i = 0; $i < count($arrBackPolish) - 2; $i++) {
                $left = $arrBackPolish[$i];
                $right = $arrBackPolish[$i + 1];
                $sign = $arrBackPolish[$i + 2];
                if ($this->trueValues($left, $right, $sign)) {

                    $newElem = $this->calcTwoElem($left, $right, $sign);
                    array_splice($arrBackPolish, $i, 3, $newElem);  
                } 
            }
        }
        return $arrBackPolish[0];
    }

    // Проверяет привильность входных значений 
    public function trueValues($left, $right, $sign)
    {
        $leftCheck = (bool) is_numeric($left);
        $rightCheck = (bool) is_numeric($right);
        $signCheck = (bool) array_key_exists($sign, $this->prioritySing);
        return ($leftCheck && $rightCheck) && $signCheck;
    }

    //Расчитывает выражение из двух элементов и знака операции
    public function calcTwoElem($left, $right, $sign)
    {
        switch ($sign) {
            case '-':
                return $left - $right;
            case '+':
                return $left + $right;
            case '*':
                return $left * $right;
            case '/':
                return $left / $right;
            case '^':
                return pow($left, $right);
        }
    }
}

?>