<?php

function getStackSizeAfterReactions($input)
{
    $inputLength = strlen($input);
    $stack = [];
    $stackSize = 0;

    for ($i = 0; $i < $inputLength; $i++) {
        $currentElement = $input[$i];

        if ($stackSize !== 0
            && ($stackTop = end($stack)) !== $currentElement
            && strcasecmp($stackTop, $currentElement) === 0
        ) {
            unset($stack[key($stack)]);
            $stackSize--;

        } else {
            $stack[] = $currentElement;
            $stackSize++;
        }
    }

    return $stackSize;
}

function solutionWithArrayFunctions($input) { // but array functions are slower, less optimal
    $inputLength = strlen($input);
    $stack = [];

    for ($i = 0; $i < $inputLength; $i++) {
        $currentElement = $input[$i];
        $stackTop = end($stack);

        if (count($stack) !== 0
            && strcmp($stackTop, $currentElement) !== 0
            && strcasecmp($stackTop, $currentElement) === 0
        ) {
            array_pop($stack);
        } else {
            array_push($stack, $currentElement);
        }
    }

    return count($stack);
}
