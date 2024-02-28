<?php
/**
 * Given an area divided into cells, each cell filled with a colour, we consider two cells as "connected" if
 * they are next to each other (either vertically or horizontally, not diagonally) and they share the same colour.
 * Find the biggest group of connected cells!
 * Return the group size and the colour name separated by a comma.
 */

$input = [
    ["GREEN",   "GREEN",   "BLUE",  "RED"],
    ["GREEN",   "BLUE",    "RED",   "BLUE"],
    ["RED",     "BLUE",    "BLUE",  "BLUE"],
];

$result = solve($input);

if ($result === "5,BLUE") {
    echo "SUCCESS\n";
} else {
    echo "FAIL\n";
}

function solve($input) {
    return "";
}

