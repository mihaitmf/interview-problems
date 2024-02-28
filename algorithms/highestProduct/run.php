<?php
/**
 * Highest product of 3 integers
 *
 * Given an array of integers, find the highest product you can get from three of the integers.
 * The input array will always have at least three integers.
 *
 * For example, given: [3, 1, 0, 5, 3, 2]
 * your function would return: 45
 * by calculating: 3 * 5 * 3 = 45
 *
 * For example, given: [3, 1, 0, 5, 3, 2, -1, -5, -2]
 * your function would return: 50
 * by calculating: 5 * -5 * -2 = 50
 */

$input = [3, 1, 0, 5, 3, 2, -1, -5, -2];
$expectedResult = 50;
$result = highestProductOf3($input);

if ($result === $expectedResult) {
    echo "SUCCESS\n";
} else {
    echo "FAIL\n";
}

function highestProductOf3($input)
{
    $highest = max($input[0], $input[1]);
    $lowest  = min($input[0], $input[1]);

    $highestProductOf2 = $input[0] * $input[1];
    $lowestProductOf2  = $input[0] * $input[1];

    $highestProductOf3 = $input[0] * $input[1] * $input[2];

    // walk through items, starting at index 2
    for ($i = 2; $i < count($input); $i++) {
        $current = $input[$i];

        // do we have a new highest product of 3?
        $highestProductOf3 = max(
            $highestProductOf3,
            $current * $highestProductOf2,
            $current * $lowestProductOf2
        );

        // do we have a new highest product of two?
        $highestProductOf2 = max(
            $highestProductOf2,
            $current * $highest,
            $current * $lowest
        );

        // do we have a new lowest product of two?
        $lowestProductOf2 = min(
            $lowestProductOf2,
            $current * $highest,
            $current * $lowest
        );

        // do we have a new highest?
        $highest = max($highest, $current);

        // do we have a new lowest?
        $lowest = min($lowest, $current);
    }

    return $highestProductOf3;
}
