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
    "decodingDigitsIntoLettersClass",
    "decodingDigitsIntoLettersFunctions",
    "solve_cryptarithmetic"
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
            $obj = new decodingDigitsIntoLetters($taskDescription);
            break;
        case 1:
            $obj = decoding($taskDescription);
            break;
        case 2:
            $obj = solve_cryptarithmetic($taskDescription);
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