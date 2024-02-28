<?php
/**
 * Product of other numbers
 *
 * You have an array of integers, and for each index you want to find the product of every integer except the integer at that index.
 * Write a function getProductsOfAllIntsExceptAtIndex() that takes an array of integers and returns an array of the products.
 * Do not use division in your solution.
 *
 * For example, given: [1, 7, 3, 4]
 * your function would return: [84, 12, 28, 21]
 * by calculating: [7 * 3 * 4, 1 * 3 * 4, 1 * 7 * 4, 1 * 7 * 3]
 */

$input = [1, 7, 3, 4];
$expectedResult = [84, 12, 28, 21];
$result = getProductsOfAllIntsExceptAtIndex($input);

if ($result === $expectedResult) {
    echo "SUCCESS\n";
} else {
    echo "FAIL\n";
}

function getProductsOfAllIntsExceptAtIndex($input) {
    $n = count($input);

    $prodsBeforeIndex = [];
    $prodsAfterIndex = [];
    $productBefore = 1;
    $productAfter = 1;
    for ($i = 0; $i < $n; $i++) {
        $prodsBeforeIndex[$i] = $productBefore;
        $productBefore *= $input[$i];

        $prodsAfterIndex[$n - 1 - $i] = $productAfter;
        $productAfter *= $input[$n - 1 - $i];
    }

    $prods = [];
    for ($i = 0; $i < $n; $i++) {
        $prods[$i] = $prodsBeforeIndex[$i] * $prodsAfterIndex[$i];
    }

    return $prods;
}
