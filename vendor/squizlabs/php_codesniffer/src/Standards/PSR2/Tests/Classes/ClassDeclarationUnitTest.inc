<?php
class ClassName extends ParentClass implements \ArrayAccess,  \Countable
{
    // constants, properties, methods
}

class ClassName extends ParentClass,AnotherParentClass implements \ArrayAccess,\Countable
{
    // constants, properties, methods
}

class ClassName
extends ParentClass
implements \ArrayAccess, \Countable
{
    // constants, properties, methods
    
}

class ClassName extends  ParentClass  implements
   \ArrayAccess,
        \Foo\Bar\Countable,
    \Serializable
{
    // constants, properties, methods

}

class ClassName extends ParentClass  implements \ArrayAccess,
    \Countable,
    \Serializable
{
    // constants, properties, methods
}

class ClassName extends ParentClass  implements
    \ArrayAccess, \Countable, \Foo\Serializable
{
    // constants, properties, methods
}

// Different indent
if ($foo) {
    class ClassName extends ParentClass implements
        \ArrayAccess,
    \Countable,
            \Serializable
    {
        // constants, properties, methods
    }
}

class Foo extends \Foo\Bar\Object
{
}

class ClassName extends ParentClass implements
    \Foo\Bar\Countable,
    \Serializable
{
    // constants, properties, methods
}

class ClassName extends ParentClass implements
    \Foo\Bar\Countable ,
    \Serializable
{
    // constants, properties, methods
}

class Test
{
    public function test()
    {
        if (1) { 1;
        }
        1 ? (1 ? 1 : 1) : 1;
    }
}

class MyClass
{
}

class MyClass
{

}

class MyClass
{
    // Foo.
}

class MyClass
{
    // Foo.

}

abstract class Test implements
    TestInterface1,
    TestInterface2
{
}

interface MyInterface extends LongInterfaceName1, LongInterfaceName2, LongInterfaceName3, LoginInterfaceName4
{
}

interface MyInterface extends
    LongInterfaceName1,
    LongInterfaceName2,
    LongInterfaceName3,
    LoginInterfaceName4
{
}

interface MyInterface extends
 LongInterfaceName1,
    LongInterfaceName2,
     LongInterfaceName3,
LongInterfaceName4
{
}

abstract
class Test
{
}

class ClassName implements

    \ArrayAccess,\Countable,
\Serializable
{
    // constants, properties, methods
}

class C1
{

} // C1

class Base
{
    protected $anonymous;

    public function __construct()
    {
        $this->anonymous = new class extends ArrayObject
        {
            public function __construct()
            {
                parent::__construct(['a' => 1, 'b' => 2]);
            }
        };
    }
}

class A extends B
    implements C
{
}

class C2
{

} // phpcs:ignore Standard.Category.Sniff

interface I1 extends
 Foo
{
}

interface I2 extends
    Bar
{
}

interface I3 extends
 Foo,
 Bar
{
}

class C1 extends
 Foo
{
}

class C2 extends
    Bar
{
}

class C3 extends Foo implements
 Bar
{
}

class C4 extends Foo implements
    Bar
{
}

class C5 extends Foo implements
 Bar,
 Baz
{
}

class C6 extends \Foo\Bar implements
  \Baz\Bar
{
}

interface I4 extends
  \Baz
  \Bar
{
}

interface I5 extends /* comment */
    \Foo\Bar
{
}

interface I6 extends // comment
    \Foo\Bar
{
}

class C7 extends // comment
    \Foo\Bar implements \Baz\Bar
{
}

class
C8
{
}

foo(
    new class {
    }
);

readonly
class Test
{
}

readonly    class Test
{
}

if (!class_exists('IndentedDeclaration')) {
    class IndentedDeclaration
    {
        function foo()
        {
        }


    }
}

// Space between modifier and class keyword would not be flagged nor fixed if newline + indentation.
final
class FinalClassWithIndentation
{
}

readonly
class ReadonlyClassWithIndentation
{
}

// And would also not be flagged if there was a comment between (not auto-fixable).
final/*comment*/class FinalClassWithComment
{
}
abstract /*comment*/  class AbstractClassWithComment
{
}

readonly
    // comment
class ReadonlyClassWithComment
{
}

// Safeguard against fixer conflict when there are namespace relative interface names in extends.
interface FooBar extends namespace\BarFoo
{
}

// Safeguard against fixer conflict when there are namespace relative interface names in a multi-line implements.
class BarFoo implements
    namespace\BarFoo
{
}

// Safeguard that the sniff ignores comments between interface names in a multiline implements.
class ClassWithMultiLineImplementsAndIgnoreAnnotation implements
    SomeInterface,
    // phpcs:disable Stnd.Cat.Sniff -- For reasons.

    \AnotherInterface
{
}

class ClassWithMultiLineImplementsAndComment implements
    SomeInterface,
    // Comment.

AnotherInterface
{
}

class ClassWithMultiLineImplementsAndCommentOnSameLineAsInterfaceName implements
    SomeInterface,
    /* Comment. */ AnotherInterface
{
}

// Verify the `CloseBraceSameLine` error code is thrown when expected.
class ClassBraceNotOnLineByItselfError
{
    public $prop;
} $foo = new ClassBraceNotOnLineByItselfError;

interface ClassBraceNotOnLineByItselfTrailingCommentIsAllowed
{
    public function myMethod();
} //end interface -- this comment is allowed.

trait ClassBraceNotOnLineByItselfTrailingAnnotationIsAllowed
{
} // phpcs:ignore Stnd.Cat.Sniff -- this comment is also allowed.

// Issue squizlabs/PHP_CodeSniffer#2621 - fix was superseded by fix for #2678.
$foo->bar(
    new class implements Bar {
        // ...
    },
);

enum BraceNotOnLineByItselfCloseTagError
{
} ?>

<?php
