                                <div>Inline HTML with indent to demonstrate the bug in the indent calculation.</div>
<?php
                                // This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
                                // the tests running into the "first PHP open tag excepted" condition breaking the tests.
                                // Tests related to that "first PHP open tag excepted" condition should go in separate files.
                                ?>
<!--
Content indent calculation should not look at first token in the stack, but only at the line containing the PHP open tag for the block.
-->
<?php
echo 'indent is correct - first on line for open tag is open tag';
?>
<?php
       echo 'indent is incorrect - first on line for open tag is inline HTML';
?>

<!--
Open tag indent calculation should not look at first token in the stack, but only at the line containing the PHP open tag for the block.
-->
<?php
// This is only here so we can have a close tag as the first token on the next line.
?><?php
// The PHP open tag should be fixed to be at the very start of the line without indent.
// And the indent of this comment should be seen as correct.
?>

<?php
// This test case file MUST always end with an unclosed long open PHP tag (with this comment) to prevent
// the tests running into the "last PHP closing tag excepted" condition breaking tests.
// Tests related to that "last PHP closing tag excepted" condition should go in separate files.
