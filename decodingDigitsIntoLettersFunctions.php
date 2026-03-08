<?PHP
/*
PHP-functions decodingDigitsIntoLetters

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