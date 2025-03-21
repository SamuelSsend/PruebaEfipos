<?php
// This test case file MUST always start with a long open PHP tag set (with this comment) to prevent
// the tests running into the "first PHP open tag excepted" condition breaking the tests.
// Tests related to that "first PHP open tag excepted" condition should go in separate files.

// TEST CASE FILE FOR TESTS RELATED TO EMPTY TAG SETS USING THE SHORT OPEN TAG ONLY.

// An empty set of short open/close tags is considered a parse error in PHP.
// The sniff should still handle these the same way as an "empty" set of long PHP open/close tags (and remove the tags).

// As this code is considered a parse error (though a non-blocking one for PHPCS), these tests are in a separate file
// and no other type of tests should be added to this file!
?>
<!-- Testing removal of complete line when stand-alone empty tag set gets removed. -->

    <?php echo
    ?>
    <?php echo ?>

<!--
Make sure the empty tag set fixer removes the complete line, including indentation for an empty tag set and ensure
that the fixer does not remove too many tokens, like new lines tokens _before_ the affected line
or indentation if there is code _after_ the removed empty tag.
-->
        <div><?php echo ?></div>
    <?php echo ?><?php echo $i; ?>


        <?php echo ?>

    <?php if (true) { ?><?php echo $i; ?>  <?php echo ?>  <?php 
    } ?>

        <div><?php echo
        ?></div>
    <?php echo
    ?><?php echo $i; ?>


    <?php echo
    ?>

    <?php if (true) { ?><?php echo $i; ?>  <?php echo
    ?>  <?php 
    } ?>

<?php
// This test case file MUST always end with an unclosed long open PHP tag (with this comment) to prevent
// the tests running into the "last PHP closing tag excepted" condition breaking tests.
// Tests related to that "last PHP closing tag excepted" condition should go in separate files.
