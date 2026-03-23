<?php
    $tablice = [
        [5, 3, 8, 1, 9, 2],
        [38, 27, 43, 3, 9, 82, 10, 15],
        [64, 25, 12, 22, 11, 90, 3, 47, 71, 38, 55, 8],
        [25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
    ];

    foreach ($tablice as $tablica) {
        $comparisons = 0;
        $n = count($tablica);

        echo "n=", count($tablica), PHP_EOL, "   | Wejście: [ ";
        foreach ($tablica as $element) {
            echo $element, " ";
        }
        echo "]", PHP_EOL;

        $tablica = mergeSort($tablica, $comparisons);
        $K = ($comparisons)/($n * log($n, 2));

        echo "   | Wyjście: [ ";
        foreach ($tablica as $element) {
            echo $element, " ";
        }
        echo "]", PHP_EOL;

        echo "   | Porównania: ", $comparisons, " | K: ", round($K, 3);
        echo PHP_EOL;
    }
    echo PHP_EOL;

    echo "Tablica 0:", PHP_EOL;
    foreach($tablice[0] as $element) {
        echo $element, " ";
    }
    echo PHP_EOL;

    $comparisons = 0;
    $mergeSorted = mergeSort($tablice[0], $comparisons);

    echo "Posortowana tablica 0 (mergeSort):", PHP_EOL;
    foreach($mergeSorted as $element) {
        echo $element, " ";
    }
    echo PHP_EOL;

    $sorted = $tablice[0];
    sort($sorted);

    echo "Posortowana tablica 0 (wbudowana funkcja):", PHP_EOL;
    foreach($sorted as $element) {
        echo $element, " ";
    }
    echo PHP_EOL;

    echo PHP_EOL;
    if ($mergeSorted === $sorted) {
        echo "Tablice posortowane odpowiednio funkcją mergeSort i funkcją wbudowaną są identyczne.";
    } else {
        echo "Tablice posortowane odpowiednio funkcją mergeSort i funkcją wbudowaną nie są identyczne.";
    }

    function mergeSort($arr, &$comparisons) {
        if (count($arr) <= 1) {
            return $arr;
        }

        $mid = (int)(count($arr) / 2);
        $left = array_slice($arr, 0, $mid);
        $right = array_slice($arr, $mid);

        $left = mergeSort($left, $comparisons);
        $right = mergeSort($right, $comparisons);

        return merge($left, $right,$comparisons);
    }

    function merge($left, $right, &$comparisons) {
        $mergedArr = [];

        $i = 0;
        $j = 0;

        $leftCount = count($left);
        $rightCount = count($right);

        while ($leftCount > $i && $rightCount > $j) {
            $comparisons++;

            if ($left[$i] > $right[$j]) {
                $mergedArr[] = $right[$j];
                $j++;
            } else {
                $mergedArr[] = $left[$i];
                $i++;
            }
        }

        while ($leftCount > $i) {
            $mergedArr[] = $left[$i];
            $i++;
        }
        while ($rightCount > $j) {
            $mergedArr[] = $right[$j];
            $j++;
        }

        return $mergedArr;
    }
?>