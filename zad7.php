<?php

// Produkty: Laptop, Monitor, Klawiatura, Mysz, Słuchawki, Kamera, Tablet, Głośnik
$oceny = [
    "Anna"    => [5, 4, null, 2, null, 3, 4, 5],
    "Bartek"  => [4, 5, 3, null, 2, 4, null, 4],
    "Celina"  => [5, 3, null, 3, null, 4, 5, null],
    "Dawid"   => [2, null, 4, 5, 3, null, 2, 3],
    "Ewa"     => [null, 4, 3, null, 5, 3, 4, 2],
    "Filip"   => [3, 5, 4, 2, null, 5, null, 4],
    "Grażyna" => [5, null, 2, 4, 3, 2, 5, null],
];
$produkty = ["Laptop","Monitor","Klawiatura","Mysz","Słuchawki","Kamera","Tablet","Głośnik"];

function pearson($userARatings, $userBRatings) {
    $filteredIndexes = [];

    for ($i = 0; $i < 8; $i++) {
        if ($userARatings[$i] !== null && $userBRatings[$i] !== null) {
            $filteredIndexes[] = $i;
        }
    }

    if (count($filteredIndexes) < 2) return 0;

    $avgA = 0;
    $avgB = 0;
    foreach ($filteredIndexes as $index) {
        $avgA += $userARatings[$index];
        $avgB += $userBRatings[$index];
    }
    $avgA /= count($filteredIndexes);
    $avgB /= count($filteredIndexes);

    $licznik = 0;
    $mianownikSuma1 = 0;
    $mianownikSuma2 = 0;
    foreach ($filteredIndexes as $index) {
        $licznik += ($userARatings[$index] - $avgA) * ($userBRatings[$index] - $avgB);
        $mianownikSuma1 += ($userARatings[$index] - $avgA) ** 2;
        $mianownikSuma2 += ($userBRatings[$index] - $avgB) ** 2;
    }
    $mianownik = sqrt($mianownikSuma1 * $mianownikSuma2);

    return $licznik / $mianownik;
}
function pred($productIndex, $oceny, $pearsonNeighbors) {
    $licznik = 0;
    $mianownik = 0;

    foreach ($pearsonNeighbors as $neighborName => $sim) {
        $rating = $oceny[$neighborName][$productIndex];
        if ($rating !== null) {
            $licznik += ($sim * $rating);
            $mianownik += abs($sim);
        }
    }

    if ($mianownik == 0) return null;

    return $licznik / $mianownik;
}

function generateRecommendations(&$recommendations, $oceny, $produkty, $chosenUsersRatings, $pearsonNeighbors) {
    for ($i = 0; $i < count($chosenUsersRatings); $i++) {
        if ($chosenUsersRatings[$i] === null) {
            $prediction = pred($i, $oceny, $pearsonNeighbors);
            if ($prediction !== null) {
                $recommendations[$produkty[$i]] = $prediction;
            }
        }
    }
}

function getPearsons($oceny, $chosenUsersName): array {
    $pearsons = [];
    foreach ($oceny as $usersName => $usersRatings) {
        if ($usersName !== $chosenUsersName) {
            $pearsons[$usersName] = pearson($oceny[$chosenUsersName], $usersRatings);
        }
    }
    return $pearsons;
}

//podobienstwo pearsona
$chosenUsersName = "Anna";
$pearsons  = getPearsons($oceny, $chosenUsersName);

arsort($pearsons);

echo "Podobieństwo Pearsona dla ".$chosenUsersName.":".PHP_EOL;
foreach ($pearsons as $pearsonName => $pearsonElement) {
    printf( "  %-8s%7.4f", "{$pearsonName}:", $pearsonElement."\n");
    echo PHP_EOL;
}

//sasiedzi
$k = 3;
$pearsonNeighbors = array_slice($pearsons, 0, $k, true);
echo PHP_EOL."k=".$k." sąsiedzi ".$chosenUsersName.": ".implode(", ", array_map(
    function ($usersName, $usersRatings) {
        return $usersName."(".number_format($usersRatings, 4).")";
    },
    array_keys($pearsonNeighbors),
    $pearsonNeighbors
)).PHP_EOL;

//przewidywanie oceny
$recommendations = [];
generateRecommendations($recommendations, $oceny, $produkty, $oceny[$chosenUsersName], $pearsonNeighbors);

arsort($recommendations);

echo PHP_EOL."Rekomendacje dla ".$chosenUsersName." (produkty nieocenione):".PHP_EOL;
$number = 0;
foreach ($recommendations as $productName => $predictedValue) {
    printf("  %d. %-13s- przewidywana ocena: %-7.2f", ++$number, $productName, $predictedValue);
    echo PHP_EOL;
}

//zimny start
$newUsersName = "Hania";
$oceny[$newUsersName] = [4, null, null, null, null, null, null, null];
$newRecommendations = [];
generateRecommendations($newRecommendations, $oceny, $produkty, $oceny["Hania"], array_slice(getPearsons($oceny, $newUsersName), 0, $k, true));
arsort($newRecommendations);

echo PHP_EOL."Zimny start (".$newUsersName.", ".count(array_filter($oceny[$newUsersName]))." ocena):".PHP_EOL;
if (count($newRecommendations) === 0) {
    echo "  Za mało wspólnych ocen z innymi użytkownikami - brak wiarygodnych korelacji.".PHP_EOL;
    echo "  Strategia: rekomenduj najpopularniejsze produkty (najwyższa średnia ocen wśród wszystkich).".PHP_EOL;
} else {
    echo PHP_EOL."Rekomendacje dla ".$newUsersName." (produkty nieocenione):".PHP_EOL;
    $number = 0;
    foreach ($newRecommendations as $productName => $predictedValue) {
        printf("  %d. %-13s- przewidywana ocena: %-7.2f", ++$number, $productName, $predictedValue);
        echo PHP_EOL;
    }
}