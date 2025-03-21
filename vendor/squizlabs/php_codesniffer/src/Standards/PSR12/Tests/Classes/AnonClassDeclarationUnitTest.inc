<?php
$instance1 = new class extends \Foo implements \HandleableInterface {
};

$instance2 = new class extends \Foo implements
    \ArrayAccess,
    \Countable,
    \Serializable
{
};

$instance3 = new class {
};
$instance4 = new class ($arg) {
};

$instance5 = new class
{

};

$instance6 = new class {

};

// Bad

$instance1 = new class( $arg )

{
};
$instance2 = new class   ($one ,  $two ) {
};
$instance3 = new class(


    $one ,
$two,
  $three, $four) {
};

$instance4 = new class
            extends \Foo {
};

$instance5 = new class($one ,
  $two , $three) extends \Foo
    implements \HandleableInterface {
};

$instance6 = new class extends \Foo
    implements \HandleableInterface {
    // Class content
};

$instance7 = new  class  extends  \Foo implements
    \One, \Two,
        \Three,
\Four,
  \Five {
    // Class content
};

if ($foo) {
    $instance8 = new class extends \Foo implements
        \One,
        \Five {
        // Class content
    };
}

$instance9 = new class (
    $one,
    $two
) extends \Foo implements \One {
    // Class content
};

$instance10 = new class   {
};

$foo->bar(
    new class implements Bar {
        // ...
    },
);

foo(
    new class {
    }
);

// Issue #3790: OpenBraceSameLine fixer should not remove open brace.
$instance = new class() extends SomeClass implements
    SomeInterface{
    public function __construct()
    {
    }
};

// PHP 8.3 readonly anonymous classes.
$anon = new readonly class {
};
$anon = new readonly    class {
};
$anon = new readonly
class {
};
