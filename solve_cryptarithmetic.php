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