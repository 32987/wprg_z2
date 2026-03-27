<?php

$dokumenty = [
    0 => "PHP jest językiem skryptowym używanym do tworzenia stron internetowych",
    1 => "Tablice w PHP mogą być indeksowane lub asocjacyjne i bardzo przydatne",
    2 => "Funkcje array_map i array_filter ułatwiają przetwarzanie tablic w PHP",
    3 => "PHP obsługuje tablice wielowymiarowe i zagnieżdżone struktury danych",
    4 => "Serwer Apache współpracuje z PHP do obsługi żądań HTTP i połączeń",
    5 => "Bazy danych MySQL są często używane razem z PHP do przechowywania",
    6 => "Funkcja usort sortuje tablice w PHP według różnych kryteriów i warunków",
    7 => "JavaScript i PHP razem tworzą dynamiczne aplikacje internetowe i serwisy",
    8 => "PHP posiada wbudowane funkcje do pracy z plikami tablicami i bazami",
    9 => "Bezpieczeństwo aplikacji PHP wymaga walidacji danych wejściowych i filtrów",
];

function printSearchResults($query, $index, $searchResults, $type) {
    echo PHP_EOL."Wyniki dla (".implode(" $type ", $query)."):".PHP_EOL;

    $loop = 0;
    foreach ($searchResults as $documentId) {
        $totalScore = 0;
        $individualScores = [];

        foreach ($query as $queryWord) {
            if (isset($index[$queryWord][$documentId])) {
                $wordCountInDocument = $index[$queryWord][$documentId];
                $individualScores[$queryWord] = $wordCountInDocument;

                $totalScore += $wordCountInDocument;
            }
        }

        echo "  ".++$loop.". Dokument ID:".$documentId." | Score:".$totalScore." (".implode(", ", array_map(
                function ($key, $value) {
                    return $key.":".$value;
                },
                array_keys($individualScores),
                array_values($individualScores)
            )).")".PHP_EOL;
    }
}

function rankTF($query, $index, $searchResults) {
    $scores = [];

    foreach ($searchResults as $documentId) {
        $totalScore = 0;
        foreach ($query as $queryWord) {
            if (isset($index[$queryWord][$documentId])) {
                $totalScore += $index[$queryWord][$documentId];
            }
        }

        $scores[$documentId] = $totalScore;
    }

    arsort($scores);
    return array_keys($scores);
}

function ANDSearch(array $query, array $index): array {
    $filteredQuery = [];
    foreach ($query as $queryWord) {
        if (isset($index[$queryWord])) {
            $filteredQuery[] = array_keys($index[$queryWord]);
        }
    }
    if (count($filteredQuery) === 0) return [];
    if (count($filteredQuery) === 1) return $filteredQuery[0];

    return array_values(call_user_func_array("array_intersect", $filteredQuery));
}

function ORSearch(array $query, array $index): array {
    $filteredQuery = [];
    foreach ($query as $queryWord) {
        if (isset($index[$queryWord])) {
            $filteredQuery[] = array_keys($index[$queryWord]);
        }
    }
    if (count($filteredQuery) === 0) return [];
    if (count($filteredQuery) === 1) return $filteredQuery[0];

    return array_unique(call_user_func_array("array_merge", $filteredQuery));
}

//budowa indeksu
function buildArray(array $dokumenty): array {
    $index = [];

    foreach ($dokumenty as $docId => $contents) {
        //zamien na male litery, usun znaki niebedace literami/spacja, podziel na slowa (explode)
        $contents = preg_replace("/[^a-zA-ZĄąĆćĘęŁłŃńÓóŚśŹźŻż\s]/u", "", strtolower($contents));
        $documentWords = explode(" ", $contents);
        $documentWords = array_filter($documentWords);

        //pomin stop words
        $stopWords = ['i', 'w', 'na', 'do', 'z', 'są', 'lub', 'być', 'może', 'jest', 'się'];
        $documentWords = array_values(array_diff($documentWords, $stopWords));

        //pomin slowa krotsze niz 3 znaki
        $tempWords = [];
        foreach ($documentWords as $word) {
            if (preg_match("/^.{3,}$/u", $word)) {
                $tempWords[] = $word;
            }
        }
        $documentWords = $tempWords;

        //buduj indeks
        $wordCounts = array_count_values($documentWords);
        foreach ($wordCounts as $slowo => $liczbaWystapien) {
            $index[$slowo][$docId] = $liczbaWystapien;
        }
    }
    return $index;
}

$index = buildArray($dokumenty);

$frequencies = [];
foreach ($index as $indexWord => $amounts) {
    $frequencies[$indexWord] = array_sum($amounts);
}
arsort($frequencies);

$topAmount = 5;

echo "Top ".$topAmount." najczęstszych słów:".PHP_EOL;
$keys = array_slice(array_keys($frequencies), 0, $topAmount);
foreach ($keys as $key) {
    echo "  '".$key."': ".$frequencies[$key]."x".PHP_EOL;
}

$query1 = ["php", "tablice"];
$searchResultsAND = rankTF($query1, $index, ANDSearch($query1, $index));
printSearchResults($query1, $index, $searchResultsAND, "AND");

$query2 = ["mysql", "javascript"];
$searchResultsOR = rankTF($query2, $index, ORSearch($query2, $index));
printSearchResults($query2, $index, $searchResultsOR, "OR");