<?php
$rekordy = [
    ["id"=>1,  "imie"=>"anna",    "wiek"=>"25",  "email"=>"anna@test.com",   "wynik"=>92.5],
    ["id"=>2,  "imie"=>"Bartosz", "wiek"=>"abc", "email"=>"bartosz@test.com","wynik"=>78.0],  // błąd: wiek
    ["id"=>3,  "imie"=>"celina",  "wiek"=>"31",  "email"=>"celina@test.com", "wynik"=>105.0], // błąd: wynik
    ["id"=>4,  "imie"=>"Dawid",   "wiek"=>"45",  "email"=>"",               "wynik"=>66.5],  // błąd: email
    ["id"=>5,  "imie"=>"EWA",     "wiek"=>"28",  "email"=>"ewa@test.com",    "wynik"=>88.0],
    ["id"=>6,  "imie"=>"filip",   "wiek"=>"130", "email"=>"filip@test.com",  "wynik"=>74.0],  // błąd: wiek
    ["id"=>7,  "imie"=>"Grażyna", "wiek"=>"52",  "email"=>"anna@test.com",   "wynik"=>91.0],  // duplikat email
    ["id"=>8,  "imie"=>"Henryk",  "wiek"=>"19",  "email"=>"henryk@test.com", "wynik"=>-5.0],  // błąd: wynik
    ["id"=>9,  "imie"=>"irena",   "wiek"=>"37",  "email"=>"irena@test.com",  "wynik"=>83.5],
    ["id"=>10, "imie"=>"JANEK",   "wiek"=>"22",  "email"=>"janek@test.com",  "wynik"=>55.0],
    ["id"=>11, "imie"=>"Kasia",   "wiek"=>"29",  "email"=>"kasia@test.com",  "wynik"=>97.0],
    ["id"=>12, "imie"=>"Leon",    "wiek"=>"41",  "email"=>"leon@test.com",   "wynik"=>62.0],
    ["id"=>13, "imie"=>"Marta",   "wiek"=>"0",   "email"=>"marta@test.com",  "wynik"=>79.5],  // błąd: wiek
    ["id"=>14, "imie"=>"norbert", "wiek"=>"33",  "email"=>"norbert@test.com","wynik"=>86.0],
    ["id"=>15, "imie"=>"Ola",     "wiek"=>"26",  "email"=>"ola@test.com",    "wynik"=>91.0],
];

//E
echo "=== Etap E: Walidacja ===".PHP_EOL;

$validatedArray = waliduj($rekordy);

if (count($validatedArray["rejected"]) > 0) {
    echo "Odrzucone rekordy (".count($validatedArray["rejected"])."):".PHP_EOL;
    foreach ($validatedArray["rejected"] as $rejectedElement) {
        echo "  - ID ".$rejectedElement["rekord"]["id"]."  (".$rejectedElement["rekord"]["imie"]."): ".implode(", ", $rejectedElement["reason"]).PHP_EOL;
    }
}

//T
$transformed = transformuj($validatedArray["valid"]);

//L
echo PHP_EOL."=== Etap L: Finalna baza (".count($transformed)." rekordów) ===".PHP_EOL;
printf("%-14s|%-6s|%-27s|%-7s|%-7s", "Imię", " Wiek", " Email", " Wynik", " Ocena");
echo PHP_EOL.str_repeat("-", 65).PHP_EOL;
foreach ($transformed as $transformedElement) {
    printf("%-13s|%6s|%-27s|%6.1f |%-7s", $transformedElement["imie"],  $transformedElement["wiek"]." ", " ".$transformedElement["email"], $transformedElement["wynik"], " ".getGradeFromScore($transformedElement["wynik"]));
    echo PHP_EOL;
}

$resultsInfo = [
    "A" => ["count" => 0, "sum" => 0],
    "B" => ["count" => 0, "sum" => 0],
    "C" => ["count" => 0, "sum" => 0],
    "D" => ["count" => 0, "sum" => 0],
];

foreach ($transformed as $transformedElement) {
    $grade = getGradeFromScore($transformedElement["wynik"]);
    $resultsInfo[$grade]["count"]++;
    $resultsInfo[$grade]["sum"] += $transformedElement["wynik"];
}

echo PHP_EOL."Rozkład ocen:".PHP_EOL;

foreach ($resultsInfo as $grade => $resultsInfoElement) {
    if ($resultsInfoElement["count"] > 0) {
        $average = $resultsInfoElement["sum"] / $resultsInfoElement["count"];
        printf("  %s: %d studentów, średnia: %.1f%%\n", $grade, $resultsInfoElement["count"], $average);
    }
}

function transformuj(array $dane): array {
    $unique = [];
    $repeatedEmails = [];

    foreach ($dane as $rekord) {
        $email = trim($rekord["email"]);

        if (isset($repeatedEmails[$email])) continue;

        $repeatedEmails[$email] = true;

        $rekord["imie"] = ucfirst(strtolower($rekord["imie"]));
        $rekord["wiek"] = (int)$rekord["wiek"];
        $rekord["wynik"] = (float)$rekord["wynik"];

        $unique[] = $rekord;
    }
    return $unique;
}

function getGradeFromScore(float $score): string
{
    if ($score >= 90) {
        return "A";
    } else if ($score >= 75) {
        return "B";
    } else if ($score >= 60) {
        return "C";
    } else {
        return "D";
    }
}

function waliduj(array $dane): array {
    $validatedArray = ["valid" => [], "rejected" => []];
    $duplicateEmails = [];

    foreach ($dane as $rekord) {
        $isValid = true;
        $rejectionReasons = [];
        $email = trim($rekord["email"]);

        if (!isStringContentInteger($rekord["wiek"]) || $rekord["wiek"] < 1 || $rekord["wiek"] > 120) {
            $rejectionReasons[] = "nieprawidłowy wiek '".$rekord["wiek"]."'";
            $isValid = false;
        }
        if (!is_numeric($rekord["wynik"])) {
            $rejectionReasons[] = "nieprawidłowy wynik: ".number_format((float)$rekord["wynik"], 1, ".", "");
            $isValid = false;
        } elseif ($rekord["wynik"] < 0.0 || $rekord["wynik"] > 100.0) {
            $rejectionReasons[] = "wynik poza zakresem [0-100]: ".number_format((float)$rekord["wynik"], 1, ".", "");
            $isValid = false;
        }
        if ($email === "") {
            $rejectionReasons[] = "pusty email";
            $isValid = false;
        }
        if ($email !== "") {
            if (isset($duplicateEmails[$email])) {
                $rejectionReasons[] = "duplikat email '".$email."'";
                $isValid = false;
            } else {
                $duplicateEmails[$email] = true;
            }
        }
        $isValid ? $validatedArray["valid"][] = $rekord : $validatedArray["rejected"][] = ["rekord" => $rekord, "reason" => $rejectionReasons];
    }
    return $validatedArray;
}

function isStringContentInteger($input): bool {
    if (!is_string($input) || $input === "") return false;
    if ($input[0] == "-") return ctype_digit(substr($input, 1));
    return ctype_digit($input);
}