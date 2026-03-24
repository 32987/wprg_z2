<?php
$dane = [];
$historia = [];

while (true) {
    echo ">> ";
    $linia = readline();
    if ($linia === false || trim($linia) === '') continue;

    $czesci = explode(' ', trim($linia), 3);
    $polecenie = strtolower($czesci[0]);

    switch ($polecenie) {
        case "slice":
            if (isset($czesci[1]) && isset($czesci[2])) {
                if (is_numeric($czesci[1]) && is_numeric($czesci[2]) && $czesci[2] >= 0) {
                    $slice = array_slice($dane, $czesci[1], $czesci[2]);
                    displayTable($slice);
                    continue 2;
                }
                displayArgumentErrorMessage();
                continue 2;
            }
            displayNoArgumentMessage($polecenie);
            continue 2;
        case "chunk":
            if (isset($czesci[1]) && !isset($czesci[2])) {
                if (is_numeric($czesci[1]) && $czesci[1] > 0) {
                    $chunks = array_chunk($dane, $czesci[1]);
                    for ($i = 0; $i < count($chunks); $i++) {
                        echo "Chunk ".($i + 1).": ";
                        displayTable($chunks[$i]);
                    }
                    continue 2;
                }
                displayArgumentErrorMessage();
                continue 2;
            }
            displayNoArgumentMessage($polecenie);
            continue 2;
        case "filter":
            $operators = [">", "<", ">=", "<=", "=="];
            if (isset($czesci[1]) && isset($czesci[2])) {
                if (in_array($czesci[1], $operators, true) && is_numeric($czesci[2])) {
                    $dane = array_filter($dane, function ($element) use ($czesci) {
                        switch ($czesci[1]) {
                            case ">": return $element > $czesci[2];
                            case "<": return $element < $czesci[2];
                            case ">=": return $element >= $czesci[2];
                            case "<=": return $element <= $czesci[2];
                            case "==": return $element == $czesci[2];
                            default: return false;
                        }
                    });
                    displayTable($dane);
                    break;
                }
                displayArgumentErrorMessage();
                continue 2;
            }
            displayNoArgumentMessage($polecenie);
            continue 2;
        case "pop":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                if (count($dane) == 0) {
                    echo "Tablica jest pusta.".PHP_EOL;
                    break;
                }
                echo array_pop($dane).PHP_EOL;
                displayTable($dane);
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "insert":
            if (isset($czesci[1]) && isset($czesci[2])) {
                if ($czesci[1] >= 0 && $czesci[1] < count($dane) && is_numeric($czesci[1]) && is_numeric($czesci[2])) {
                    array_splice($dane, $czesci[1], 0, $czesci[2]);
                    displayTable($dane);
                    break;
                }
                displayArgumentErrorMessage();
                continue 2;
            }
            displayNoArgumentMessage($polecenie);
            continue 2;
        case "delete":
            if (isset($czesci[1]) && !isset($czesci[2])) {
                if (is_numeric($czesci[1]) && $czesci[1] >= 0 && $czesci[1] < count($dane)) {
                    array_splice($dane, (int)$czesci[1], 1);
                    displayTable($dane);
                    break;
                }
                displayArgumentErrorMessage();
                continue 2;
            }
            displayNoArgumentMessage($polecenie);
            continue 2;
        case "push":
            if (isset($czesci[1]) && !isset($czesci[2])) {
                if (is_numeric($czesci[1])) {
                    array_push($dane, $czesci[1]);
                    displayTable($dane);
                    break;
                }
                displayArgumentErrorMessage();
                continue 2;
            }
            displayNoArgumentMessage($polecenie);
            continue 2;
        case "save":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                echo json_encode($dane).PHP_EOL;
                continue 2;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "help":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                echo "push v".PHP_EOL
                    ."pop".PHP_EOL
                    ."insert idx v".PHP_EOL
                    ."delete idx".PHP_EOL
                    ."sort".PHP_EOL
                    ."rsort".PHP_EOL
                    ."filter op v".PHP_EOL
                    ."unique".PHP_EOL
                    ."reverse".PHP_EOL
                    ."chunk n".PHP_EOL
                    ."slice od ile".PHP_EOL
                    ."stats".PHP_EOL
                    ."show".PHP_EOL
                    ."reset".PHP_EOL
                    ."save".PHP_EOL
                    ."history".PHP_EOL
                    ."help".PHP_EOL
                    ."exit".PHP_EOL;
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "history":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                if (count($historia) == 0) {
                    echo "Historia poleceń jest pusta.".PHP_EOL;
                    continue 2;
                }
                for ($i = 0; $i < count($historia); $i++) {
                    echo ($i + 1).": ".$historia[$i].PHP_EOL;
                }
                continue 2;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "stats":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                if (count($dane) == 0) {
                    echo "Tablica jest pusta.".PHP_EOL;
                    break;
                }
                echo "Suma: "
                    .array_sum($dane)
                    ." | Średnia: "
                    .(array_sum($dane)/count($dane))
                    ." | Min: "
                    .minimum($dane)
                    ." | Max: "
                    .maximum($dane)
                    .PHP_EOL;
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "unique":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                $dane = array_unique($dane);
                displayTable($dane);
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "reverse":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                $dane = array_reverse($dane);
                displayTable($dane);
                continue 2;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "show":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                displayTable($dane);
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "reset":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                $dane = [];
                displayTable($dane);
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "sort":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                sort($dane);
                displayTable($dane);
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "rsort":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                rsort($dane);
                displayTable($dane);
                break;
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        case "exit":
            if (!isset($czesci[1]) && !isset($czesci[2])) {
                echo "Do widzenia!".PHP_EOL;
                exit(0);
            }
            displayUnknownCommandMessage($linia);
            continue 2;
        default:
            displayUnknownCommandMessage($linia);
            continue 2;
    }
    addToHistory($historia, $linia);
}
function displayArgumentErrorMessage() {
    echo "Niepoprawne argumenty.".PHP_EOL;
}
function displayUnknownCommandMessage($linia) {
    echo "Nieznane polecenie: ".$linia.PHP_EOL;
}
function displayNoArgumentMessage($polecenie) {
    echo "Brak argumentu dla: ".$polecenie.PHP_EOL;
}
function addToHistory(&$historia, $linia) {
    $historia[] = $linia;
    $historia = array_slice($historia, -10);
}
function displayTable($dane) {
    echo "[".implode(", ", $dane)."]".PHP_EOL;
}
function minimum($arr) {
    if (count($arr) == 1) {
        return $arr[0];
    }
    if (count($arr) < 1) {
        return null;
    }

    $min = $arr[0];
    foreach ($arr as $element) {
        if ($element < $min) {
            $min = $element;
        }
    }
    return $min;
}
function maximum($arr) {
    if (count($arr) == 1) {
        return $arr[0];
    }
    if (count($arr) < 1) {
        return null;
    }

    $max = $arr[0];
    foreach ($arr as $element) {
        if ($element > $max) {
            $max = $element;
        }
    }
    return $max;
}
?>