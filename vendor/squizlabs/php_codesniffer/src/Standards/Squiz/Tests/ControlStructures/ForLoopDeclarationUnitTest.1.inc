<?php

// Valid.
for ($i = 0; $i < 10; $i++) {
}

// Invalid.
for ( $i = 0; $i < 10; $i++ ) {
}

for ($i = 0;  $i < 10;  $i++) {
}

for ($i = 0 ; $i < 10 ; $i++) {
}

for ($i = 0;$i < 10;$i++) {
}

// The works.
for ( $i = 0 ;  $i < 10 ;  $i++ ) {
}

for ($i = 0; $i < 10;) {
}

for ($i = 0; $i < 10; ) {
}

for ($i = 0; ; $i++) {
}
for ($i = 0;; $i++) {
}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for ($i = 0; $i < 10; $i++) {
}
for ( $i = 0; $i < 10; $i++ ) {
}
for (  $i = 0; $i < 10; $i++  ) {
}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0

for (         ; $i < 10; $i++) {
}
for (; $i < 10; $i++) {
}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for ( ; $i < 10; $i++ ) {
}
for (         ; $i < 10; $i++ ) {
}
for (; $i < 10; $i++ ) {
}

for ( $i = 0; $i < 10; ) {
}
for ( $i = 0; $i < 10;) {
}
for ( $i = 0; $i < 10;     ) {
}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0

// Test handling of comments and inline annotations.
for ( /*phpcs:enable*/ $i = 0 /*start*/ ;    /*end*/$i < 10/*comment*/; $i++ /*comment*/   ) {
}

// Test multi-line FOR control structure.
for (
    $i = 0;
    $i < 10;
    $i++
) {
}

// Test multi-line FOR control structure with comments and annotations.
for (
    $i = 0; /* Start */
    $i < 10; /* phpcs:ignore Standard.Category.SniffName -- for reasons. */
    $i++ // comment

) {
}

// Test fixing each error in one go. Note: lines 78 + 82 contain trailing whitespace on purpose.
for (
      

      $i = 0

      ; 

      $i < 10

      ;

      $i++


) {
}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for (



      $i = 0

      ;

      $i < 10

      ;

      $i++


) {
}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0

// Test with semicolon not belonging to for.
for ($i = function () {
    return $this->i  ;    
}; $i < function () {
    return $this->max; 
}; $i++) {
}
for ($i = function () {
    return $this->i; 
}; $i < function () {
    return $this->max; 
}  ;   $i++) {
}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration ignoreNewlines true
for (
    $i = 0;
    $i < 5;
    $i++
) {
    // body here
}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration ignoreNewlines false
