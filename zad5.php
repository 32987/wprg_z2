<?php
$transakcje = [
    ["id"=>1,  "data"=>"2024-01-15","kategoria"=>"Elektronika","kwota"=>1200.00],
    ["id"=>2,  "data"=>"2024-01-22","kategoria"=>"Dom",        "kwota"=>350.00],
    ["id"=>3,  "data"=>"2024-02-03","kategoria"=>"Elektronika","kwota"=>800.00],
    ["id"=>4,  "data"=>"2024-02-14","kategoria"=>"Odzież",     "kwota"=>250.00],
    ["id"=>5,  "data"=>"2024-02-28","kategoria"=>"Dom",        "kwota"=>420.00],
    ["id"=>6,  "data"=>"2024-03-05","kategoria"=>"Elektronika","kwota"=>1500.00],
    ["id"=>7,  "data"=>"2024-03-12","kategoria"=>"Odzież",     "kwota"=>180.00],
    ["id"=>8,  "data"=>"2024-03-19","kategoria"=>"Dom",        "kwota"=>290.00],
    ["id"=>9,  "data"=>"2024-01-08","kategoria"=>"Odzież",     "kwota"=>310.00],
    ["id"=>10, "data"=>"2024-01-30","kategoria"=>"Elektronika","kwota"=>950.00],
    ["id"=>11, "data"=>"2024-02-10","kategoria"=>"Dom",        "kwota"=>600.00],
    ["id"=>12, "data"=>"2024-03-25","kategoria"=>"Odzież",     "kwota"=>430.00],
    ["id"=>13, "data"=>"2024-01-18","kategoria"=>"Elektronika","kwota"=>2100.00],
    ["id"=>14, "data"=>"2024-02-22","kategoria"=>"Dom",        "kwota"=>175.00],
    ["id"=>15, "data"=>"2024-03-08","kategoria"=>"Elektronika","kwota"=>670.00],
    ["id"=>16, "data"=>"2024-01-25","kategoria"=>"Odzież",     "kwota"=>520.00],
    ["id"=>17, "data"=>"2024-02-17","kategoria"=>"Elektronika","kwota"=>1350.00],
    ["id"=>18, "data"=>"2024-03-14","kategoria"=>"Dom",        "kwota"=>480.00],
    ["id"=>19, "data"=>"2024-01-12","kategoria"=>"Dom",        "kwota"=>230.00],
    ["id"=>20, "data"=>"2024-02-05","kategoria"=>"Odzież",     "kwota"=>390.00],
];

$nazwyMiesiecy = [
    "01" => "Styczeń",
    "02" => "Luty",
    "03" => "Marzec",
    "04" => "Kwiecień",
    "05" => "Maj",
    "06" => "Czerwiec",
    "07" => "Lipiec",
    "08" => "Sierpień",
    "09" => "Wrzesień",
    "10" => "Październik",
    "11" => "Listopad",
    "12" => "Grudzień",
];

$pivot = [];
$miesiace = [];
$kategorie = [];

$categoryColumnWidth = 14;
$dataColumnWidth = 8;

foreach ($transakcje as $t) {
    $kategoria = $t["kategoria"];
    $miesiac = substr($t["data"], 0, 7);
    $kwota = $t["kwota"];

    $kategorie[$kategoria] = true;
    $miesiace[$miesiac] = true;

    if (!isset($pivot[$kategoria][$miesiac])) {
        $pivot[$kategoria][$miesiac] = 0.0;
    }

    $pivot[$kategoria][$miesiac] += $kwota;
}

$miesiace = array_keys($miesiace);
$kategorie = array_keys($kategorie);

sort($kategorie);
sort($miesiace);

printf("%-{$categoryColumnWidth}s", "Kategoria");
foreach($miesiace as $m) {
    printf(" | %{$dataColumnWidth}s", $nazwyMiesiecy[substr($m, -2, 2)]);
}
echo PHP_EOL;

echo str_repeat("-", $categoryColumnWidth);
foreach ($miesiace as $m) {
    echo str_repeat("-", $dataColumnWidth + 3);
}
echo PHP_EOL;

foreach ($kategorie as $k) {
    printf("%-{$categoryColumnWidth}s", $k);
    foreach ($miesiace as $m) {
        printf(" | %{$dataColumnWidth}.2f", $pivot[$k][$m] ?? 0);
    }
    echo PHP_EOL;
}

$transactionsInCategories = [];

foreach ($transakcje as $t) {
    $kategoria = $t["kategoria"];
    $kwota = $t["kwota"];

    $transactionsInCategories[$kategoria][] = $kwota;
}

ksort($transactionsInCategories);

echo PHP_EOL."Odchylenia standardowe (σ):".PHP_EOL;
$maxSigma = 0;
$maxCategory = "";
foreach ($transactionsInCategories as $kategoria => $kwoty) {
    $n = count($kwoty);
    $srednia = array_sum($kwoty) / $n;
    $sigma = odchylenieStandardowe($kwoty);

    if ($sigma > $maxSigma) {
        $maxSigma = $sigma;
        $maxCategory = $kategoria;
    }

    printf("%-14s:", "  ".$kategoria);
    echo " σ=".round($sigma, 2)." (n=".$n.", avg=".round($srednia, 2)." PLN)".PHP_EOL;
}

echo PHP_EOL."Kategoria o największej zmienności: ".$maxCategory." (σ=".round($maxSigma, 2).")".PHP_EOL;

function odchylenieStandardowe($tab) {
    $n = count($tab);

    if ($n === 0) {
        return 0;
    }

    $srednia = array_sum($tab) / $n;
    $sumaKwadratow = 0;

    foreach ($tab as $t) {
        $sumaKwadratow += ($t - $srednia) ** 2;
    }

    return sqrt($sumaKwadratow/$n);
}
?>