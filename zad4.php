<?php

function s_push(array &$stos, $val): void {
    array_splice($stos, count($stos), 0, [$val]);
}
function s_pop(array &$stos) {
    $top = $stos[count($stos) - 1];
    array_splice($stos, -1, 1);
    return $top;
}
function s_peek(array $stos) {
    return $stos[count($stos) - 1];
}

function validation(string $text): bool {
    $stack = [];
    $matchMap = [
        ")" => "(",
        "]" => "[",
        "}" => "{",
    ];

    for ($i = 0; $i < strlen($text); $i++) {
        $character = $text[$i];

        if ($character === "(" || $character === "[" || $character === "{") {
            s_push($stack, $character);
        } elseif ($character === ")" || $character === "]"|| $character === "}") {
            if (count($stack) === 0) return false;
            $top = s_pop($stack);
            if ($top !== $matchMap[$character]) return false;
        }
    }
    return count($stack) === 0;
}

function onp(string $expr): float {
    $stack = [];
    $tokens = explode(' ', $expr);

    foreach ($tokens as $token) {
        if (is_numeric($token)) {
            s_push($stack, (float)$token);
        } else {
            $topB = s_pop($stack);
            $topA = s_pop($stack);

            switch ($token) {
                case "+": $result = $topA + $topB; break;
                case "-": $result = $topA - $topB; break;
                case "*": $result = $topA * $topB; break;
                case "/": $result = $topA / $topB; break;
                default: echo "Nieznany operator: ".$token.PHP_EOL;
            }
            s_push($stack, $result);
        }
    }
    return s_pop($stack);
}

$wyrazenia_ONP = [
    "5 2 + 3 *",
    "15 7 1 1 + - / 3 * 2 1 1 + + -",
    "4 13 5 / +",
    "2 3 + 4 * 5 -",
    "100 50 25 / -",
];

$napisy_nawiasy = [
    "[({()})]",
    "((())",
    "{[()]}",
    "([)]",
    "",
];

$buffer = array_fill(0, 5, null);
$pos = 0;

for ($i = 0; $i < count($wyrazenia_ONP); $i++) {
    $nawiasy = $napisy_nawiasy[$i];
    $wyrazenieONP = $wyrazenia_ONP[$i];

    $isValid = validation($nawiasy);
    $result = onp($wyrazenieONP);

    $buffer[$pos % 5] = $result;
    $pos++;

    $resultString = (string)((floor($result) == $result) ? (int)$result : $result);

    printf("%-24s%-4s  |%-38s%-10s", ("[".($i + 1)."] Nawiasy \"$nawiasy\": "), ($isValid ? "OK" : "BŁĄD"), (" ONP \"$wyrazenieONP\""), ("= $resultString"));
    echo PHP_EOL;
}

echo PHP_EOL."Bufor cykliczny (ostatnie 5 wyników): [".implode(", ", array_map(function($b) {
    return ((floor($b) == $b) ? (int)$b : $b);
}, $buffer))."]".PHP_EOL;