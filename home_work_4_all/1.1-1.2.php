<?php
    
    $start = microtime(true);

    $temp = new MathTree("(36-25)*89-(36+41)^3+45*((25^2)+9499)"); 
    echo $temp;
    echo $temp->calkTree();

    $end = microtime(true);
    $finish = $end - $start;
    echo "<br>" . "Время выполнения: " . $finish;
    
    class MathTree
    {
        public $tree;
        public function __construct($expression)
        {
            $this->expression = $this->prepare($expression);
            $this->buildTree($this->expression);
        }

        // Возвращает дерево
        public function __toString()
        {
            ob_start();
            echo "Арифметическое дерево: {$this->expression}<br>";
            print_r($this->tree);
            
            return ob_get_clean();
        }

        public function buildTree(string $expression)
        {
            $this->tree = new MathTreeNode($expression);
        }

        // Метод подготавливает введенное выражение
        public function prepare($expression)
        {
            // Подготавливаем выражение (убираем пробелы)
            $exp = str_replace(" ", "", $expression);
           
            if (preg_match('/^[\d\.\-\*\/\+\^()]+$/', $exp )) {
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

        public function calkTree()
        {
            $result = $this->tree->calkNode();
            return "<br>Решение выражения {$this->expression}:  {$result}";
        }    
    }

    class MathTreeSheet
    {
        public function __construct($value)
        {
            $this->value = $value * 1;
        }
    }

    class MathTreeNode
    {
        public function __construct($expression)
        {
            $this->expression= $expression;
            $this->createNode($expression);
        }
        public $sign;
        public $right;
        public $left;
        public $expression;
        // данные текущей точки перегиба 
        public $PointInflection = null;
        
        public $countBrace = 0;
        // Приоритет операторов
        public $prioritySing = [
            '^' => 3,
            '*' => 2,
            '/' => 2,
            '+' => 1,
            '-' => 1
        ];

        // Создает узлы дерева 
        public function createNode($expression)
        {
            $this->getPointInflection($expression);

            while ($this->PointInflection['brace'] !== 0) {
                $this->expression = substr($expression, 1, strlen($expression) - 2);
                $this->getPointInflection($this->expression);
            } 
            
            $this->sign = $this->PointInflection['sign'];
            $left = substr($this->expression, 0, $this->PointInflection['ind']);
            $right = substr($this->expression, $this->PointInflection['ind'] + 1);
            
            if ($this->checkSign($left)) {
                
                $this->left = new MathTreeNode($left);
            } else {
                $this->left = new MathTreeSheet($left);
            }
            if ($this->checkSign($right)) {
                $this->right = new MathTreeNode($right);
            } else {
                $this->right = new MathTreeSheet($right);
            }

        }

        // Метод возрващает true если в оставшемся выражении есть знаки арифм. операций и false - если нет.
        public function checkSign($str)
        {
            foreach ($this->prioritySing as $key=>$value) {
                
                 if (strpos($str, $key) !== false) {
                    return true;
                 }
            }
            return false;
        }

        // Метод определяющий точку перегиба или ее отсутствие
        public function getPointInflection(string $val)
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
                    
                    if (is_null($this->PointInflection)) {
                        $this->setInflectionPoint($val, $i);
                    }
                    if ($this->PointInflection['brace'] > $this->countBrace) {
                        $this->setInflectionPoint($val, $i);
                    }
                    if ($this->PointInflection['brace'] === $this->countBrace && 
                        $this->getPriority($this->PointInflection['sign']) >= $this->getPriority($val[$i])) {
                            $this->setInflectionPoint($val, $i);
                    }
                }
            }
        }

        // Обнуление данных точки перегиба 
        public function resetInflectionPoint() 
        {
            $this->PointInflection = null;
        }

        // Установка новой точки перегиба   
        public function setInflectionPoint($val, $i)
        {
            $this->resetInflectionPoint();
            $this->PointInflection = array('ind' => $i, 'sign' => $val[$i], 'brace' => $this->countBrace);
        }

        // Получение приоритета
        public function getPriority($item) 
        {
            return $this->prioritySing[$item];
        }

        // Решает выражение в узле
        public function calkNode()
        {
            $left = null;
            $right = null;
            
            if (!is_null($this->left->sign)) {
                $left = $this->left->calkNode();
            } else if (!is_null($this->left->value)){
                $left = $this->left->value;
            }
            if (!is_null($this->right->sign)) {
                $right = $this->right->calkNode();
            } else if (!is_null($this->right->value)){
                $right = $this->right->value;
            }
            switch ($this->sign) {
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