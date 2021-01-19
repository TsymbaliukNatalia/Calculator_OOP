<?php
class Calculator
{
    // оголошуємо строку в якій проводитиме операції калькулятор
    private $str;
    // оголошуємо масив з яким проводитимемо всі операції
    private $arr;
    // оголошуємо строку в яку запишемо результат всіх операцій
    private $result;
    public $error_flag;
    public $error_masage;
    const PI = 3.14;
    public function __construct($str)
    {
        $this->str = $str;
    }
    // розбиваємо строку на масив
    private function getArrFromString()
    {
        $this->arr = array();
        $this->arr = str_split($this->str);
    }
    public function validateExpression()
    {
        $this->getArrFromString();
        // масив з обов'язковими символами
        $mandatory_symbols = array();
        $mandatory_symbols = ["+", "-", "*", "/"];
        // масив з дозволеними символами
        $permitted_symbols = array();
        $permitted_symbols = ["P", "p", "I", "i", "+", "-", "*", "/", ".", ",", "(", ")", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        // перевіряємо чи введена строка містить не менше 3 символів
        if (strlen($this->str) < 3) {
            $this->error_flag = true;
            $this->error_masage = "Вираз має містити не менше трьох символів!";
            return false;
        }
        // перевіряємо чи присутній хоч один з обо'язкових символів
        $present_mandatory_symbol = false;
        foreach ($this->arr as $operand) {
            if (in_array($operand, $mandatory_symbols)) {
                $present_mandatory_symbol = true;
            }
        }
        // якщо немає жодного обо'язкового символа видаємо повідомлення про помилку
        if (!$present_mandatory_symbol) {
            $this->error_flag = true;
            $this->error_masage = "Вираз повинен містити хоча б один із символів (*,/,+,-)!";
            return false;
        }
        // перевіряємо чи є в масиві хоча б один заборонений символ
        $unauthorized_simvol = false;
        foreach ($this->arr as $operand) {
            if (!in_array($operand, $permitted_symbols)) {
                $unauthorized_simvol = true;
            }
        }
        // якщо в масиві хоча б один заборонений символ видаємо повідомлення про помилку
        if ($unauthorized_simvol) {
            $this->error_flag = true;
            $this->error_masage = "Введені некоректні символи!";
            return false;
        }
        // перевіряємо ділення на 0
        foreach ($this->arr as $key => $operand) {
            if ($operand == "/" && $this->arr[$key + 1] == 0) {
                $this->error_flag = true;
                $this->error_masage = "На нуль ділити не можна!";
                return false;
            }
        }
        return true;
    }
    /*
replacePiInArray замінює символи, що складають словосполучення "рі"
на значення константи PI і повертає масив з заміненим значенням
*/
    private function replacePiInArray()
    {
        foreach ($this->arr as $key => $value) {
            // перевіряємо чи пара двох елементів масиву складає комбінацію "рі"
            // якщо так перший елемент масиву замінюємо на значення константи РІ,
            // а наступний видаляємо і перепроставляємо індеки масиву
            if (($value == "P" || $value == "p") && ($this->arr[$key + 1] == "I" || $this->arr[$key + 1] == "i")) {
                $this->arr[$key] = self::PI;
                unset($this->arr[$key + 1]);
                array_values($this->arr);
            }
        }
    }
    /* breakArrayIntoOperators розбиває масив на числа і оператори
повертає масив з набору чисел і операторів
 */
    private function breakArrayIntoOperators()
    {
        // створюємо масив символів які не будемо перетворювати в число
        $simvol = array();
        $simvol = ["(", ")", "*", "/", "+", "-"];
        // створюємо новий масив який міститиме числа і знаки
        $newArr = array();
        // створюємо строку в яку записуватимемо число
        $string_value = "";
        foreach ($this->arr as $key => $value) {
            // якщо символ не входить в масив не числових знаків
            // значить це цифра і потрібно записати її в підсумковий рядок
            if (!in_array($value, $simvol) && $value != self::PI) {
                // якщо в числі є кома, заміняємо її на крапку для подальшого перетворення у float
                if ($value == ",") {
                    $value = ".";
                }
                $string_value = $string_value . $value;
                // якщо значення останнє в масиві записуємо його в $newArr
                if ($key == (count($this->arr) - 1)) {
                    settype($string_value, "float");
                    $newArr[] = $string_value;
                }
            } else if ($string_value == "") {
                // якщо немає чисел для запису, записуємо в масив оператор
                $newArr[] = $value;
            } else {
                // якщо є число для запису, змінюємо його тип і записуємо в масив
                // після записуємо в масив оператор
                settype($string_value, "float");
                $newArr[] = $string_value;
                $string_value = "";
                $newArr[] = $value;
            }
        }
        $this->arr = $newArr;
    }
    /* getMultiplicationAndDivision виконує множення і ділення в переданому масиві
повертає масив де всі операції множення і ділення замінені на результати виконих операцій
 */
    private function getMultiplicationAndDivision(array $arr): array
    {
        // допоки в масиві є множення чи ділення виконуємо по одній операції за раз
        // і записуємо в масив
        while (in_array("*", $arr) || in_array("/", $arr)) {
            $arr = array_values($this->MultiplicationAndDivision($arr));
        }
        return $arr;
    }
    /* MultiplicationAndDivision виконує одну операцію множення
чи ділення і повертає масив де дана операція замінена на результат 
її виконання
 */
    private function MultiplicationAndDivision(array $arr): array
    {
        foreach ($arr as $key => $value) {
            if ($value == "/") {
                $arr[$key - 1] = $arr[$key - 1] / $arr[$key + 1];
                unset($arr[$key + 1]);
                unset($arr[$key]);
                array_values($arr);
                return $arr;
            }
            if ($value == "*") {
                $arr[$key - 1] = $arr[$key - 1] * $arr[$key + 1];
                unset($arr[$key + 1]);
                unset($arr[$key]);
                array_values($arr);
                return $arr;
            }
        }
    }
    /* getAdditionAndSubtraction виконує всі операції додавання і віднімання в масиві
повертає загальний результат операцій
 */
    private function getAdditionAndSubtraction($arr)
    {
        // виконуємо додавання і віднімання і
        // повертаємо результат
        if (in_array("+", $arr) || in_array("-", $arr)) {
            $result = 0;
            foreach ($arr as $key => $value) {
                if ($key == 0) {
                    $result = $value;
                } else {
                    if ($value == "+") {
                        $result = $result + $arr[$key + 1];
                    }
                    if ($value == "-") {
                        $result = $result - $arr[$key + 1];
                    }
                }
            }
            return $result;
        }
    }
    /* getResultOfOperations приймає масив і повертає результат
всіх проведених в ньому математичних операцій
 */
    private function getResultOfOperations($arr)
    {
        // спочатку виконуємо все множення і ділення в масиві
        // і повертаємо масив результатів
        $arr = $this->getMultiplicationAndDivision($arr);
        // якщо все множення і ділення виконано, а операторів
        // більше не залишилось, повертаємо результат
        if (count($arr) == 1) {
            return $arr[0];
        } else {
            // якщо додавання і віднімання ще можна виконати,
            // виконуємо і повертаємо результат
            $result = $this->getAdditionAndSubtraction($arr);
            return $result;
        }
    }
    /*
actionInParentheses виконує всі операції в підмасиві з перших 
дужок у масиві
 */
    private function actionInParentheses($arr)
    {
        // оголошуємо масив який буде містити підмасив з дужок
        $newArr = array();
        foreach ($arr as $key => $value) {
            // якщо при переборі масиву тропляється перша дужка
            // записуємо ключ як початок нашого підмасиву
            if ($value == "(") {
                $begin = $key;
            }
            // якщо натрапляємо на закриваючу дужку, записуємо ключ
            // як кінець нашого підмасиву і перериваємо цикл
            if ($value == ")") {
                $end = $key;
                break;
            }
        }
        // записуємо в новий масив всі операції з нашого підмасиву
        // виключаючи дужки
        for ($i = $begin + 1; $i < $end; $i++) {
            $newArr[] = $arr[$i];
        }
        // записуємо результат дій з підмасивом
        $result = $this->getResultOfOperations($newArr);
        // заміняємо відкриваючу дужку на результат операцій
        $arr[$begin] = $result;
        // видаляємо всі значення підмасиву крім значення на місці першої дужки
        for ($i = $begin + 1; $i <= $end; $i++) {
            unset($arr[$i]);
        }
        return array_values($arr);
    }
    /* getRezultInParentheses приймає масив і виконує всі дії в дужках
повертає масив зі всіма виконаними в дужках діями
 */
    private function getResultInParentheses($arr)
    {
        while (in_array("(", $arr) || in_array(")", $arr)) {
            $arr = $this->actionInParentheses($arr);
        }
        return $arr;
    }
    private function setResult()
    {
        $this->getArrFromString();
        $this->replacePiInArray();

        $this->breakArrayIntoOperators();

        $arr = $this->arr;

        $arr = $this->getResultInParentheses($arr);
        $this->result = $this->getResultOfOperations($arr);
    }
    public function getResult()
    {
        $this->setResult();
        return $this->result;
    }
}
