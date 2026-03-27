<?php

function sito(int $n): array {
    $A = array_fill(0, $n + 1, true);
    $A[0] = $A[1] = false;

    for ($i = 2; $i * $i <= $n; $i++) {
        if ($A[$i]) {
            for ($j = $i * $i; $j <= $n; $j += $i) {
                $A[$j] = false;
            }
        }
    }

    $primeNumbers = [];
    for ($i = 2; $i <= $n; $i++) {
        if ($A[$i]) {
            $primeNumbers[] = $i;

        }
    }

    return $primeNumbers;
}

$primes = sito(500);
$primeSet = array_flip($primes);

echo "Liczby pierwsze [1-100] (bloki po 10):".PHP_EOL;
$primesBelow100 = array_filter($primes, fn ($p) => $p <= 100);
$chunks = array_chunk($primesBelow100, 10);

foreach ($chunks as $chunk) {
    echo "[".implode(", ", $chunk)."]".PHP_EOL;
}

echo PHP_EOL."Gęstosc liczb pierwszych:".PHP_EOL;

$intervals = [
    [1, 100],
    [101, 200],
    [201, 300],
    [301, 400],
    [401, 500],
];

foreach ($intervals as [$from, $to]) {
    $count = 0;
    foreach ($primes as $prime) {
        if ($prime >= $from && $prime <= $to) {
            $count++;
        }
    }

    $mid = ($from + $to) / 2;
    $theoretical = ($to - $from) / log($mid);

    printf("%-20s%3d (teoretycznie: ~%.1f)\n", "Przedział [".$from."-".$to."]:", $count, $theoretical);
}

echo PHP_EOL."Goldbach:".PHP_EOL;

$maxPairs = 0;
$numberWithMax = 0;
$goldbachPairsFor30 = [];

for ($n = 4; $n <= 200; $n += 2) {
    $pairs = [];

    foreach ($primes as $prime) {
        if ($prime > $n / 2) break;
        $q = $n - $prime;
        if (isset($primeSet[$q])) {
            $pairs[] = [$prime, $q];
        }
    }

    if (count($pairs) > $maxPairs) {
        $maxPairs = count($pairs);
        $numberWithMax = $n;
    }

    if ($n == 30) {
        $goldbachPairsFor30 = $pairs;
    }
}

echo "Goldbach - najwięcej par w [4, 200]: Liczba ".$numberWithMax." (".$maxPairs." par)".PHP_EOL;
$pairsString = array_map(fn($pair) => "[{$pair[0]}+{$pair[1]}]", $goldbachPairsFor30);
echo "Pary Goldbacha dla 30: ".implode(", ", $pairsString).PHP_EOL;