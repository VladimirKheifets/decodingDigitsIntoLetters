<?PHP
/*
PHP-class decodingDigitsIntoLetters

Version: 1.0, 2025-12-22
Author: Vladimir Kheifets (vladimir.kheifets@online.de)
Copyright (c) 2025 Vladimir Kheifets All Rights Reserved

The script provides a solution to one task of the GCHQ 2025 Christmas Challenge.
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
    private $allFormulasLetters = [];
    private $formulas;
    private $foundLettersDigit = [];
    public $report;
    public $result;
    public $decoded;
    public $error = false;
    public $errorMsg = "";

    function __construct($taskDescription){
        $this -> report = $taskDescription;
        preg_match_all("/.+\=\S{3}/m",$taskDescription, $match);
        $this -> formulas = $match[0];
        if(count($match[0]) == 3){
           $this -> formulas = $match[0];
           foreach($match[0] as $nFormula => $formula)
                $this -> allFormulasLetters[$nFormula] = $this -> getFormulaLetters($formula);
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
            $this ->  foundLettersDigit =  $foundLettersDigit;
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
        return false;
    }
    #####################################################

    private function decoding(){
        $hr = "\n--------------------------------\n";
        $allDig = range(0,9);
        $formulas = $this -> formulas;
        $formulas[] = $formulas[0];
        foreach($formulas as $nFormula => $formula)
        {
            $foundLettersDigit =  $this -> foundLettersDigit;
            $this -> report .=  "$hr\nFormula: $formula\n";
            $formulaLetters = $this -> allFormulasLetters[$nFormula>2?0:$nFormula];
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
                    $fin = $this -> calcFormula([$L1, $L2]);
                    if($fin) break;
                }
            }

            if(!$fin) $this -> getFoundLettersDigit();
            $this -> outputDecodedLetters();
        }
    }
}

?>