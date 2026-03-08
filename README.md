# PHP scripts decodingDigitsIntoLetters

### Version: 1.0, 2025-12-22

Author: Vladimir Kheifets <vladimir.kheifets@online.de>

Copyright &copy; 2025 Vladimir Kheifets All Rights Reserved

The PHP scripts provides a solution to one task of the GCHQ 2025 Christmas Challenge.

(GCHQ - UK's intelligence, security and cyber agency)

[https://www.gchq.gov.uk/sites/default/files/documents/gchq%20christmas%20challenge%202025.pdf](https://www.gchq.gov.uk/sites/default/files/documents/gchq%20christmas%20challenge%202025.pdf)

```

    The letters in TWO UV PAIRS have the values
    0,1,2,…,9 in some order, with each letter representing
    a different digit.
    UV+UV+V=VAR
    RxPxP=AIR
    SO+SO=VOW
    What is 1234567 ?


```
Demo:
[https://www.alto-booking.com/developer/decodingDigitsIntoLetters](https://www.alto-booking.com/developer/decodingDigitsIntoLetters)


## 1. File index.php
```php
<!--

Demo PHP scripts decodingDigitsIntoLetters

Version: 1.0, 2025-12-22
Author: Vladimir Kheifets (vladimir.kheifets@online.de)
Copyright (c) 2025 Vladimir Kheifets All Rights Reserved

The scripts provides a solution to one task of the GCHQ 2025 Christmas Challenge.
(GCHQ - UK's intelligence, security and cyber agency)

The letters in TWO UV PAIRS have the values
0,1,2,…,9 in some order, with each letter representing
a different digit.
UV+UV+V=VAR
RxPxP=AIR
SO+SO=VOW
What is 1234567 ?

-->


<html>
<head>
<title>Demo PHP scripts decodingDigitsIntoLetters</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,
user-scalable=no, user-scalable=0" >
<style>
body{
 font-family: arial;
 font-size: 12pt;
 padding: 20pt 0pt 20pt 20pt;
}
</style>
</head>
<body>

<?PHP
#The letters in TWO UV PAIRS have

$taskDescription = <<<EOF
The letters in TWO UV PAIRS have
the values 0,1,2,…,9 in some order,
with each letter representing
a different digit.

UV+UV+V=VAR
RxPxP=AIR
SO+SO=VOW

What is 1234567?

EOF;


echo "<pre>";

$fNames =
[
    "solve_cryptarithmetic",
    "decodingDigitsIntoLettersClass",
    "decodingDigitsIntoLettersFunctions",
];

foreach($fNames as $i => $fName)
{
    $fName .= ".php";
    echo "\n\n<h3>$fName</h3>";

    include_once($fName);

    $start = hrtime(true);
    switch ($i)
    {
        case 0:
            $obj = solve_cryptarithmetic($taskDescription);
            break;
        case 1:
            $obj = new decodingDigitsIntoLetters($taskDescription);
            break;
        case 2:
            $obj = decoding($taskDescription);
            break;
    }

    $end=hrtime(true);
    $duration[$i]= ($end-$start)/1e+6;

    echo  "\nreport:\n\n", $obj-> report;
    if($obj-> error)
        echo  "\n\nerrorMsg: ", $obj-> errorMsg;
    else
    {
        echo  "\n\nresult: ", $obj-> result;
        echo  "\n\ndecoded ";
        print_r($obj -> decoded);
    }

}

##############################################
echo "\n<h3>Duration:</h3>";
foreach($fNames as $i => $fName)
{
    $fName .= ".php";
    echo "<b>$fName</b>: {$duration[$i]}s\n";
}
?>
</body>
</html>
```
## 1. PHP-class and functions decodingDigitsIntoLetters
## 1.1 PHP-class decodingDigitsIntoLetters
### File decodingDigitsIntoLettersClass.php
```php
<?PHP
/*
PHP-class decodingDigitsIntoLetters

Version: 1.0, 2025-12-22
Author: Vladimir Kheifets (vladimir.kheifets@online.de)
Copyright (c) 2025 Vladimir Kheifets All Rights Reserved

The script provides a solution to one task of the GCHQ 2025 Christmas competition.
(GCHQ - UK's intelligence, security and cyber agency)

The letters in TWO UV PAIRS have the values
0,1,2,…,9 in some order, with each letter representing
a different digit.
UV+UV+V=VAR
RxPxP=AIR
SO+SO=VOW
What is 1234567 ?

*/

class decodingDigitsIntoLetters{
    private $taskDescription;
    private $tmpLettersAlpha = [];
    private $formulaLetters = [];
    private $formulas;
    private $foundLettersDigit = [];
    public $report;
    public $result;
    public $decoded;
    public $error = false;
    public $errorMsg = "";

    function __construct($taskDescription){
        $this -> report = $taskDescription;
        preg_match_all("/(\p{Lu}{1,2}(\+|x|\-\:)\p{Lu}{1,2}(\+|x|\-\:)\p{Lu}{1,2}|\p{Lu}{1,2}(\+|x|\-\:)\p{Lu}{1,2})\=\p{Lu}{3}/m", $taskDescription, $match);
        if(count($match[0]) == 3){
           $this -> formulas = $match[0];
           $this -> decoding();
        }
        else
        {
            $this -> error = true;
            $this -> errorMsg = "Three formulas were not found in the task description";
        }

    }

    #########################################################################
    private function getLettersDigit($resDigit, $lettersVal){
        $tmpLettersAlpha = $this -> tmpLettersAlpha;
        $formulaLetters = $this -> formulaLetters;
        foreach($formulaLetters[1] as $j => $letter)
        {
            $tmpLettersAlpha[$letter][]=$resDigit[$j];
        }

        foreach($formulaLetters[6] as $inLetter){
            $tmpLettersAlpha[$inLetter][]=$lettersVal[$inLetter];
        }
        $this -> tmpLettersAlpha = $tmpLettersAlpha;
    }
    #########################################################################
    private function getFoundLettersDigit(){
        $foundLettersDigit = $this -> foundLettersDigit;
        $tmpLettersAlpha = $this -> tmpLettersAlpha;
        foreach($tmpLettersAlpha as $letter => $letterDigit){
            $tmp = array_unique( $letterDigit);
            if(count($tmp) == 1)
                $foundLettersDigit[$letter]=$tmp[0];
        }

        $this -> foundLettersDigit = $foundLettersDigit;
        $this -> tmpLettersAlpha = [];
    }
    #########################################################################
    private function outputDecodedLetters(){
        $foundLettersDigit =  $this -> foundLettersDigit;
        $buf = array_flip($foundLettersDigit);
        ksort($buf);
        $this -> report .=  "\nDecoded:\n";
        foreach($buf as $dig => $letter)
            $this -> report .=  "$letter => $dig\n";

        if(count($buf)>8)
        {
            $this -> result = substr(implode("", array_values($buf)),1,7);
            $this -> report .=  "\n\n1234567 is ";
            $this -> report .=  $this -> result;
            $this -> decoded = array_flip($buf);
        }
    }
    #########################################################################
    private function getFormulaLetters($formula){
        $buf = explode("=", $formula);
        $formulaLetters = [];
        $formulaLetters[] = $buf[1];
        $formulaLetters[] = str_split($buf[1]);
        preg_match_all("/\p{Lu}/", $buf[0], $matches);
        $letters = array_unique($matches[0]);
        $formulaLetters[] = $letters;
        preg_match_all("/(\p{Lu}+|[\+\-x])/", $buf[0], $matches);
        $formulaLetters[] = $matches[0];
        foreach($formulaLetters[1] as $i => $resLetter){
            $key = array_search($resLetter, $formulaLetters[2]);
            if($key !== false)
            {
                $formulaLetters[5][$i] = $key;
                $reverseKey = $key == 1?0:1;
                $formulaLetters[6][] = $formulaLetters[2][$reverseKey];
            }
        }

        return $formulaLetters;
    }

    #####################################################

    private function calcFormula($inValues){
        $foundLettersDigit = $this -> foundLettersDigit;
        $foundDigitsLetter = array_flip($foundLettersDigit);
        $formulaLetters = $this -> formulaLetters;
        foreach($formulaLetters[2] as $i => $Letter)
            $lettersVal[$Letter] = $inValues[$i];

        $buf = "\$res=";
        foreach($formulaLetters[3] as $Letters)
        {
            if(preg_match("/\p{Lu}/", $Letters))
            {
                $tmp = "";
                foreach(str_split($Letters) as $Letter)
                    $tmp .= $lettersVal[$Letter];
                $buf .= intval($tmp);

            }
            else
                $buf .= $Letters=="x"?"*":$Letters;
        }

        eval($buf.";");

        $resDigits = array_unique(str_split((string) $res));

        if(count($resDigits) > 2)
        {

            $resOK = true;

            foreach($resDigits as $i => $resDigit)
            {
                $letter = $formulaLetters[1][$i];
                if(array_key_exists($resDigit, $foundDigitsLetter))
                {
                        if( $foundDigitsLetter[$resDigit] != $letter)
                        {
                            $resOK = false;
                            break;
                        }
                }
            }

            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            if($resOK)
            {
                foreach($formulaLetters[5] as $i => $inValKey){
                    if($inValKey !== false)
                    {
                        if($resDigits[$i] == $inValues[$inValKey])
                        {
                            $this -> getLettersDigit($resDigits, $lettersVal);
                        }
                    }
                }
            }
        }
    }

    #####################################################

    private function decoding(){
        $hr = "\n--------------------------------\n";
        $allDig = range(0,9);
        $formulas = $this -> formulas;

        foreach($formulas as $nFormula => $formula)
        {
            $foundLettersDigit =  $this -> foundLettersDigit;
            $this -> report .=  "$hr\nFormula: $formula\n";

            $formulaLetters = $this -> getFormulaLetters($formula);
            $this -> formulaLetters = $formulaLetters;

            $arrK = array_diff($allDig, array_values($foundLettersDigit));

            foreach($formulaLetters[2] as $i => $letter)
            {
                if(array_key_exists($letter, $foundLettersDigit))
                    $arr[$i] = [$foundLettersDigit[$letter]];
                else
                    $arr[$i] = $arrK;
            }

            foreach($arr[0] as $L1)
            {
                foreach($arr[1] as $L2)
                {
                    $this -> calcFormula([$L1, $L2]);
                }
            }

            $this -> getFoundLettersDigit();
            $this -> outputDecodedLetters();
        }

        $this -> foundLettersDigit["U"] = $foundLettersDigit["U"] =
         ($foundLettersDigit["V"]*100 + $foundLettersDigit["A"]*10 + $foundLettersDigit["R"]
          - $foundLettersDigit["V"]*3)/20;

        $this -> report .=  <<<HTML
        $hr\nFormula: {$formulas[0]}

        (U x 10 + V) + (U x 10 + V) + V
        = V x 100 + A x 10 + R

        U = (
            {$foundLettersDigit["V"]} x 100
            + {$foundLettersDigit["A"]} x 10
            + {$foundLettersDigit["R"]}
            - {$foundLettersDigit["V"]} x 3
            )/20

        U = {$foundLettersDigit["U"]}

        HTML;
        $this -> outputDecodedLetters();
    }

}
?>
```
## 1.2 PHP-functions decodingDigitsIntoLetters

### File decodingDigitsIntoLettersFunctions.php
```php
<?PHP
/*
PHP-functions decodingDigitsIntoLetters

Version: 1.0, 2025-12-22
Author: Vladimir Kheifets (vladimir.kheifets@online.de)
Copyright (c) 2025 Vladimir Kheifets All Rights Reserved

The script provides a solution to one task of the GCHQ 2025 Christmas competition.
(GCHQ - UK's intelligence, security and cyber agency)

The letters in TWO UV PAIRS have the values
0,1,2,…,9 in some order, with each letter representing
a different digit.
UV+UV+V=VAR
RxPxP=AIR
SO+SO=VOW
What is 1234567 ?

*/

#########################################################################
function getLettersDigit($formulaLetters, $resDigit, $lettersVal, &$tmpLettersAlpha){
    foreach($formulaLetters[1] as $j => $letter)
    {
        $tmpLettersAlpha[$letter][]=$resDigit[$j];
    }

    foreach($formulaLetters[6] as $inLetter){
        $tmpLettersAlpha[$inLetter][]=$lettersVal[$inLetter];
    }
}
#########################################################################
function getFoundLettersDigit(&$tmpLettersAlpha, &$foundLettersDigit){

    foreach($tmpLettersAlpha as $letter => $letterDigit){
        $tmp = array_unique( $letterDigit);
        if(count($tmp) == 1)
            $foundLettersDigit[$letter]=$tmp[0];
    }

    $tmpLettersAlpha=[];
}
#########################################################################
function outputDecodedLetters($foundLettersDigit, &$report){
    $buf = array_flip($foundLettersDigit);
    ksort($buf);
    $report .=  "\nDecoded:\n";
    foreach($buf as $dig => $letter)
        $report .=  "$letter => $dig\n";
    if(count($buf)>8)
    {
       $result = substr(implode("", array_values($buf)),1,7);
       $report .= "\n\n1234567 is $result";
       return (object)
       [
           "error" => false,
           "errorMsg" => "",
           "result" => $result,
           "report" => $report,
           "decoded" => array_flip($buf)
       ];
    }
}
#########################################################################
function getFormulaLetters($formula){
    $buf = explode("=", $formula);
    $out = [];
    $out[] = $buf[1];
    $out[] = str_split($buf[1]);
    preg_match_all("/\p{Lu}/", $buf[0], $matches);
    $letters = array_unique($matches[0]);
    $out[] = $letters;
    preg_match_all("/(\p{Lu}+|[\+\-x])/", $buf[0], $matches);
    $out[] = $matches[0];
    foreach($out[1] as $i => $resLetter){
        $key = array_search($resLetter, $out[2]);
        if($key !== false)
        {
            $out[5][$i] = $key;
            $reverseKey = $key == 1?0:1;
            $out[6][] = $out[2][$reverseKey];
        }
    }

    return $out;
}
#########################################################################
function calcFormula($inValues, $formulaLetters, &$tmpLettersAlpha, &$foundLettersDigit){

    $foundDigitsLetter = array_flip($foundLettersDigit);

    foreach($formulaLetters[2] as $i => $Letter)
        $lettersVal[$Letter] = $inValues[$i];

    $рredefResStr = "";
    foreach($formulaLetters[1] as $i => $Letter)
        if(array_key_exists($Letter, $foundLettersDigit))
            $рredefResStr .= $foundLettersDigit[$Letter];
    $рredefRes = strlen($рredefResStr) == 3?intval($рredefResStr):0;

    $buf = "\$res=";
    foreach($formulaLetters[3] as $Letters)
    {
        if(preg_match("/\p{Lu}/", $Letters))
        {
            $tmp = "";
            foreach(str_split($Letters) as $Letter)
                $tmp .= $lettersVal[$Letter];
            $buf .= intval($tmp);

        }
        else
            $buf .= $Letters=="x"?"*":$Letters;
    }

    eval($buf.";");

    if($res > 0 AND $res == $рredefRes)
    {
        foreach($lettersVal as $Letter => $digit)
            if(!array_key_exists($Letter, $foundLettersDigit))
                 $foundLettersDigit[$Letter] = $digit;
        return true;
    }
    else
    {
        $resDigits = array_unique(str_split((string) $res));
        if(count($resDigits) > 2)
        {
            $resOK = true;
            foreach($resDigits as $i => $resDigit)
            {
                $letter = $formulaLetters[1][$i];
                if(array_key_exists($resDigit, $foundDigitsLetter))
                {
                        if( $foundDigitsLetter[$resDigit] != $letter)
                        {
                            $resOK = false;
                            break;
                        }
                }
            }

            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            if($resOK)
            {
                foreach($formulaLetters[5] as $i => $inValKey){
                    if($inValKey !== false)
                    {
                        if($resDigits[$i] == $inValues[$inValKey])
                        {
                            getLettersDigit($formulaLetters, $resDigits, $lettersVal, $tmpLettersAlpha);
                        }
                    }
                }
            }
        }
    }
}
#########################################################################
function decoding($taskDescription){
    $hr = "\n--------------------------------\n";
    $report = $taskDescription;
    $allDig = range(0,9);
    $foundLettersDigit = [];
    preg_match_all("/(\p{Lu}{1,2}(\+|x|\-\:)\p{Lu}{1,2}(\+|x|\-\:)\p{Lu}{1,2}|\p{Lu}{1,2}(\+|x|\-\:)\p{Lu}{1,2})\=\p{Lu}{3}/m", $taskDescription, $match);
    if(count($match[0])<3)
    {
       return (object)
       [
           "report" => $report,
           "error" => true,
           "errorMsg" => "Three formulas were not found in the task description"
       ];
    }

    $formulas = $match[0];
    foreach($match[0] as $nFormula => $formula)
        $allFormulasLetters[$nFormula] = getFormulaLetters($formula);
    $formulas[] = $formulas[0];

    foreach($formulas as $nFormula => $formula)
    {
        $report .=  "$hr\nFormula: $formula\n";

        $formulaLetters = $allFormulasLetters[$nFormula>2?0:$nFormula];

        $arrK = array_diff($allDig, array_values($foundLettersDigit));

        foreach($formulaLetters[2] as $i => $letter)
        {
            if(array_key_exists($letter, $foundLettersDigit))
                $arr[$i] = [$foundLettersDigit[$letter]];
            else
                $arr[$i] = $arrK;
        }

        foreach($arr[0] as $L1)
        {
            foreach($arr[1] as $L2)
            {
                $fin = calcFormula([$L1, $L2], $formulaLetters, $tmpLettersAlpha, $foundLettersDigit);
                if($fin) break;
            }
        }

        if(!$fin) getFoundLettersDigit($tmpLettersAlpha, $foundLettersDigit);
        $out = outputDecodedLetters($foundLettersDigit, $report);
    }

    return $out;
}
?>
```
## 2 Function solve_cryptarithmetic
## 2.1 Python-Function solve_cryptarithmetic from Google
### File solve_cryptarithmetic.py

```python

from itertools import permutations

def solve_cryptarithmetic():
    # Letters involved: T, W, O, U, V, P, A, I, R, S
    # There are 10 distinct letters: T, W, O, U, V, P, A, I, R, S
    letters = ('T', 'W', 'O', 'U', 'V', 'P', 'A', 'I', 'R', 'S')

    # We need to find a permutation of 0-9 for these 10 letters
    digits = range(10)


    for p in permutations(digits):
        //print(p);
        d = dict(zip(letters, p))

        # Leading digit checks
        if d['U'] == 0 or d['V'] == 0 or d['R'] == 0 or d['S'] == 0:
            continue

        # UV + UV + V = VAR
        # (10*U + V) + (10*U + V) + V = 100*V + 10*A + R
        # 20*U + 3*V = 100*V + 10*A + R
        if 20 * d['U'] + 3 * d['V'] != 100 * d['V'] + 10 * d['A'] + d['R']:
            continue

        # R * P * P = AIR
        # R * P^2 = 100*A + 10*I + R
        if d['R'] * (d['P']**2) != 100 * d['A'] + 10 * d['I'] + d['R']:
            continue

        # SO + SO = VOW
        # (10*S + O) + (10*S + O) = 100*V + 10*O + W
        # 20*S + 2*O = 100*V + 10*O + W
        if 20 * d['S'] + 2 * d['O'] != 100 * d['V'] + 10 * d['O'] + d['W']:
            continue

        return d

result = solve_cryptarithmetic()

print(result)

# {'T': 8, 'W': 0, 'O': 5, 'U': 6, 'V': 1, 'P': 9, 'A': 2, 'I': 4, 'R': 3, 'S': 7}```

```

## 2.2 Аdapting Python code into PHP
### File solve_cryptarithmetic.php
```php
<?PHP
function factorial($n) {
    return ($n <= 1) ? 1 : $n * factorial($n - 1);
}

function countPermutations($n, $r) {
    return factorial($n) / factorial($n - $r);
}


function permutations(array $elements)
{
    if (count($elements) <= 1)
    {
        yield $elements;
    }
    else
    {
        foreach (permutations(array_slice($elements, 1)) as $permutation)
        {
            foreach (range(0, count($elements) - 1) as $i)
            {
                yield array_merge(
                    array_slice($permutation, 0, $i),
                    [$elements[0]],
                    array_slice($permutation, $i)
                );
            }
        }
    }
}

function solve_cryptarithmetic($taskDescription){
    $report = $taskDescription;
    $report .= "\n--------------------------------\n";
    preg_match_all("/\p{Lu}{1}/", $taskDescription, $match);
    $letters = array_values(array_unique($match[0]));

    if(count($letters)<10)
    {
       return (object)
       [
           "report" => $report,
           "error" => true,
           "errorMsg" => "Incorrect task description!\n
           Fewer than ten unique capital\n
           letters were found in the text."
       ];
    }
    $digits = array_keys($letters);
    $report .="\nThe following arrays are defined from the task description:\n\n";
    foreach (["letters","digits"] as $name)
        $report .= "$$name = array('". implode("','",$$name)."');\n";
    $countDig = count($digits);
    $countPer = countPermutations($countDig, $countDig);
    $report .= "\nNumber of permutations: $countPer";
    foreach (permutations($digits) as $permutation)
    {
        $d = array_combine( $letters, $permutation);

        ## Leading $d igit checks
        if( $d ['U'] == 0 or $d ['V'] == 0 or $d ['R'] == 0 or $d ['S'] == 0)
        continue;

        ## UV + UV + V = VAR
        ## (10*U + V) + (10*U + V) + V = 100*V + 10*A + R
        ## 20*U + 3*V = 100*V + 10*A + R
        if( 20 * $d ['U'] + 3 * $d ['V'] != 100 * $d ['V'] + 10 * $d ['A'] + $d ['R'])
        continue;

        ## R * P * P = AIR
        ## R * P^2 = 100*A + 10*I + R
        if( $d ['R'] * ( $d['P']**2 ) != 100 * $d ['A'] + 10 * $d ['I'] + $d ['R'])
        continue;

        ## SO + SO = VOW
        ## (10*S + O) + (10*S + O) = 100*V + 10*O + W
        ## 20*S + 2*O = 100*V + 10*O + W
        if( 20 * $d ['S'] + 2 * $d ['O'] != 100 * $d ['V'] + 10 * $d ['O'] + $d ['W'])
        continue;
        $buf = array_flip($d);
        ksort($buf);
        $result = substr(implode("", array_values($buf)),1,7);

        $report .= "\n\n1234567 is $result";
        return (object)
        [
           "report" => $report,
           "error" => false,
           "result" => $result,
           "decoded" => array_flip($buf)
        ];
    }
}
?>
```

## 3. Output from index.php
```

    decodingDigitsIntoLettersClass.php

    report:

    The letters in TWO UV PAIRS have
    the values 0,1,2,…,9 in some order,
    with each letter representing
    a different digit.

    UV+UV+V=VAR
    RxPxP=AIR
    SO+SO=VOW

    What is 1234567?

    --------------------------------

    Formula: UV+UV+V=VAR

    Decoded:
    V => 1
    R => 3

    --------------------------------

    Formula: RxPxP=AIR

    Decoded:
    V => 1
    A => 2
    R => 3
    I => 4
    P => 9

    --------------------------------

    Formula: SO+SO=VOW

    Decoded:
    W => 0
    V => 1
    A => 2
    R => 3
    I => 4
    O => 5
    S => 7
    P => 9

    --------------------------------

    Formula: UV+UV+V=VAR

    Decoded:
    W => 0
    V => 1
    A => 2
    R => 3
    I => 4
    O => 5
    U => 6
    S => 7
    P => 9


    1234567 is VARIOUS

    result: VARIOUS

    decoded Array
    (
        [W] => 0
        [V] => 1
        [A] => 2
        [R] => 3
        [I] => 4
        [O] => 5
        [U] => 6
        [S] => 7
        [P] => 9
    )


    decodingDigitsIntoLettersFunctions.php

    report:

    The letters in TWO UV PAIRS have
    the values 0,1,2,…,9 in some order,
    with each letter representing
    a different digit.

    UV+UV+V=VAR
    RxPxP=AIR
    SO+SO=VOW

    What is 1234567?

    --------------------------------

    Formula: UV+UV+V=VAR

    Decoded:
    V => 1
    R => 3

    --------------------------------

    Formula: RxPxP=AIR

    Decoded:
    V => 1
    A => 2
    R => 3
    I => 4
    P => 9

    --------------------------------

    Formula: SO+SO=VOW

    Decoded:
    W => 0
    V => 1
    A => 2
    R => 3
    I => 4
    O => 5
    S => 7
    P => 9

    --------------------------------

    Formula: UV+UV+V=VAR

    Decoded:
    W => 0
    V => 1
    A => 2
    R => 3
    I => 4
    O => 5
    U => 6
    S => 7
    P => 9


    1234567 is VARIOUS

    result: VARIOUS

    decoded Array
    (
        [W] => 0
        [V] => 1
        [A] => 2
        [R] => 3
        [I] => 4
        [O] => 5
        [U] => 6
        [S] => 7
        [P] => 9
    )


    solve_cryptarithmetic.php

    report:

    The letters in TWO UV PAIRS have
    the values 0,1,2,…,9 in some order,
    with each letter representing
    a different digit.

    UV+UV+V=VAR
    RxPxP=AIR
    SO+SO=VOW

    What is 1234567?

    --------------------------------

    The following arrays are defined from the task description:

    $letters = array('T','W','O','U','V','P','A','I','R','S');
    $digits = array('0','1','2','3','4','5','6','7','8','9');

    Number of permutations: 3628800

    1234567 is VARIOUS

    result: VARIOUS

    decoded Array
    (
        [W] => 0
        [V] => 1
        [A] => 2
        [R] => 3
        [I] => 4
        [O] => 5
        [U] => 6
        [S] => 7
        [T] => 8
        [P] => 9
    )

    Duration:
    decodingDigitsIntoLettersClass.php: 0.492571s
    decodingDigitsIntoLettersFunctions.php: 0.462043s
    solve_cryptarithmetic.php: 620.049595s


```
