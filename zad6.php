<?php

// Czasy w minutach od północy: 8:00 = 480, 8:30 = 510, 9:00 = 540 itd.
$zadania = [
    ["id"=>1,  "nazwa"=>"T01", "start"=>480,  "koniec"=>600],   // 8:00–10:00
    ["id"=>2,  "nazwa"=>"T02", "start"=>510,  "koniec"=>720],   // 8:30–12:00
    ["id"=>3,  "nazwa"=>"T03", "start"=>540,  "koniec"=>660],   // 9:00–11:00
    ["id"=>4,  "nazwa"=>"T04", "start"=>600,  "koniec"=>690],   // 10:00–11:30
    ["id"=>5,  "nazwa"=>"T05", "start"=>660,  "koniec"=>780],   // 11:00–13:00
    ["id"=>6,  "nazwa"=>"T06", "start"=>690,  "koniec"=>840],   // 11:30–14:00
    ["id"=>7,  "nazwa"=>"T07", "start"=>720,  "koniec"=>810],   // 12:00–13:30
    ["id"=>8,  "nazwa"=>"T08", "start"=>780,  "koniec"=>900],   // 13:00–15:00
    ["id"=>9,  "nazwa"=>"T09", "start"=>840,  "koniec"=>960],   // 14:00–16:00
    ["id"=>10, "nazwa"=>"T10", "start"=>480,  "koniec"=>540],   // 8:00–9:00
    ["id"=>11, "nazwa"=>"T11", "start"=>570,  "koniec"=>630],   // 9:30–10:30
    ["id"=>12, "nazwa"=>"T12", "start"=>750,  "koniec"=>870],   // 12:30–14:30
    ["id"=>13, "nazwa"=>"T13", "start"=>900,  "koniec"=>990],   // 15:00–16:30
    ["id"=>14, "nazwa"=>"T14", "start"=>495,  "koniec"=>555],   // 8:15–9:15
    ["id"=>15, "nazwa"=>"T15", "start"=>870,  "koniec"=>930],   // 14:30–15:30
];

//algorytm zachlanny
$zadania1 = $zadania;

usort($zadania1, fn($a, $b) => $a["koniec"] <=> $b["koniec"]);

$wybraneZadania = [$zadania1[0]];
$koniecOstatnioWybranego = $zadania1[0]["koniec"];

for ($i = 1; $i < count($zadania1); $i++) {
    $zadanie = $zadania1[$i];
    if ($zadanie["start"] >= $koniecOstatnioWybranego) {
        $wybraneZadania[] = $zadanie;
        $koniecOstatnioWybranego = $zadanie["koniec"];
    }
}

echo "Algorytm zachłanny (jedna sala):".PHP_EOL;
echo "  Wybrane zadania (".count($wybraneZadania)."): ".implode(", ", array_map(fn($zadanie) => $zadanie["nazwa"], $wybraneZadania)).PHP_EOL;
echo "  Kolejność decyzji: ".implode(" → ", array_map(fn($zadanie) => $zadanie["nazwa"]."(".minutyNaCzas($zadanie["start"])."-".minutyNaCzas($zadanie["koniec"]).")", $wybraneZadania)).PHP_EOL;

//wykrywanie kolizji
$iloscKonfliktowPerZadanie = [];

foreach ($zadania as $zadA) {
    $iloscKonfliktow = 0;
    foreach ($zadania as $zadB) {
        if ($zadA["id"] !== $zadB["id"] && czyKoliduje($zadA, $zadB)) {
            $iloscKonfliktow++;
        }
    }
    $iloscKonfliktowPerZadanie[$zadA["nazwa"]] = $iloscKonfliktow;
}

arsort($iloscKonfliktowPerZadanie);
$najbardziejKonfliktoweZadanieNazwa = array_key_first($iloscKonfliktowPerZadanie);
$najbardziejKonfliktoweZadanieIlosc = $iloscKonfliktowPerZadanie[$najbardziejKonfliktoweZadanieNazwa];

echo PHP_EOL."Konflikty:".PHP_EOL;
echo "  Najbardziej konfliktowe: ".$najbardziejKonfliktoweZadanieNazwa." (".$najbardziejKonfliktoweZadanieIlosc." kolizji z innymi zadaniami)".PHP_EOL;

//minimalna liczba sal
$zadania2 = $zadania;

usort($zadania2, fn($zadA, $zadB) => $zadA["start"] <=> $zadB["start"]);

$sale = [];

foreach ($zadania2 as $zadanie) {
    $czyPrzypisano = false;

    for ($i = 0; $i < count($sale); $i++) {
        $ostatnieZadanie = $sale[$i]["zadania"][count($sale[$i]["zadania"]) - 1];
        if ($ostatnieZadanie["koniec"] <= $zadanie["start"]) {
            $sale[$i]["zadania"][] = $zadanie;
            $czyPrzypisano = true;
            break;
        }
    }

    if (!$czyPrzypisano) {
        $sale[] = [
            "numer" => count($sale) + 1,
            "zadania" => [$zadanie],
        ];
    }
}

echo PHP_EOL."Minimalna liczba sal: ".count($sale).PHP_EOL;
foreach ($sale as $sala) {
    echo "  Sala ".$sala["numer"].": ".implode(", ", array_map(fn($zadanie) => $zadanie["nazwa"]."(".minutyNaCzas($zadanie["start"])."-".minutyNaCzas($zadanie["koniec"]).")", $sala["zadania"])).PHP_EOL;
}

function minutyNaCzas(int $m): string {
    $h = floor($m / 60);
    $min = $m % 60;
    return sprintf("%d:%02d", $h, $min);
}
function czyKoliduje($zadA, $zadB): bool {
    return max($zadA["start"], $zadB["start"]) < min($zadA["koniec"], $zadB["koniec"]);
}