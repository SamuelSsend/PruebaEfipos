<?php

$var = (int) $var2;
$var = (int)$var2;
$var = (int)  $var2;

$var = (integer) $var2;
$var = (integer)$var2;
$var = (integer)  $var2;

$var = (string) $var2;
$var = (string)$var2;
$var = (string)    $var2;

$var = (float) $var2;
$var = (float)$var2;
$var = (float)  $var2;

$var = (double) $var2;
$var = (double)$var2;
$var = (double)  $var2;

$var = (real) $var2;
$var = (real)$var2;
$var = (real)  $var2;

$var = (array) $var2;
$var = (array)$var2;
$var = (array)  $var2;

$var = (bool) $var2;
$var = (bool)$var2;
$var = (bool)  $var2;

$var = (boolean) $var2;
$var = (boolean)$var2;
$var = (boolean)  $var2;

$var = (object) $var2;
$var = (object)$var2;
$var = (object)  $var2;

$var = (unset) $var2;
$var = (unset)$var2;
$var = (unset)          $var2;

$var = b"binary $foo";
$var = b"binary string";
$var = b'binary string';
$var = (binary) $string;
$var = (binary)$string;

$var = (boolean) /* comment */ $var2;

$var = (int)
    $var2;

if ((string) // phpcs:ignore Standard.Cat.SniffName -- for reasons.
    $x === 'test'
) {
}

// phpcs:set Generic.Formatting.SpaceAfterCast ignoreNewlines true
$var = (int)
    $var1 + (bool)  $var2;

if ((string) // phpcs:ignore Standard.Cat.SniffName -- for reasons.
    $x === 'test'
) {
}
// phpcs:set Generic.Formatting.SpaceAfterCast ignoreNewlines false

// phpcs:set Generic.Formatting.SpaceAfterCast spacing 2
$var = (int) $var2;
$var = (string)$var2;
$var = (array)  $var2;
$var = (unset)        $var2;
$var = (boolean) /* comment */ $var2;

$var = (integer)
    $var2;

// phpcs:set Generic.Formatting.SpaceAfterCast spacing 0
$var = (int) $var2;
$var = (string)$var2;
$var = (array)  $var2;
$var = (unset)        $var2;
$var = (boolean) /* comment */ $var2;

$var = (integer)
    $var2;

// phpcs:set Generic.Formatting.SpaceAfterCast ignoreNewlines true
$var = (int)
    $var1 + (bool)  $var2;
// phpcs:set Generic.Formatting.SpaceAfterCast ignoreNewlines false
// phpcs:set Generic.Formatting.SpaceAfterCast spacing 1

$var = (boolean)/* comment */ $var2;

$var = (  int )$spacesInsideParenthesis;
$var = (	int		)$tabsInsideParenthesis;
