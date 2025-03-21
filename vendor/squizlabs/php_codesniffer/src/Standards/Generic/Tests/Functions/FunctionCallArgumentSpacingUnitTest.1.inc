<?php

$result = myFunction();
$result = myFunction($arg1, $arg2);
$result = myFunction($arg1, $arg2);
$result = myFunction($arg1, $arg2);
$result = myFunction($arg1,  $arg2);
$result = myFunction($arg1, $arg2, $arg3, $arg4, $arg5);
$result = myFunction($arg1, $arg2, $arg3, $arg4, $arg5);
$result = myFunction($arg1, $arg2 = array());
$result = myFunction($arg1, $arg2 =array());
$result = myFunction($arg1, $arg2= array());
$result = myFunction($arg1, $arg2=array());

$result = myFunction(
    $arg1, 
    $arg2 = array(), 
    $arg3, 
    $arg4, 
    $arg5
);

throw new Exception(
    "This is some massive string for a message", 
    $cause
);

// Function definitions are ignored
function myFunction($arg1,$arg2)
{
}

function myFunction($arg1,$arg2)
{
}

function myFunction($arg1=1,$arg2=2)
{
}


function myFunction($arg1 = 1,$arg2 = 2)
{
}

$key = array_search($this->getArray($one,  $two, $three), $this->arrayMap);
$this->error($obj->getCode(), $obj->getMessage(), $obj->getFile(), $obj->getLine());

make_foo($string /*the string*/, true /*test*/);
make_foo($string/*the string*/,   /*test*/ true);
make_foo($string /*the string*/, /*test*/ true);

class MyClass
{
    function myFunction()
    {
        blah($foo, "{{$config['host']}}", "{$config}", "hi there{}{}{{{}{}{}}");
    }
}

// Function definition, not function call, so should be ignored
function &myFunction($arg1=1,$arg2=2)
{
}

return array_udiff(
    $foo,
    $bar,
    function ($a, $b) {
        $foo='bar';
        return $foo;
    }
);

var_dump(
    <<<FOO
foo
FOO
    ,
<<<BAR
bar
BAR
    , <<<BAZ
baz
BAZ
    , <<<'NOW'
now
NOW
    ,  <<<'THEN'
then
THEN
);

if (in_array($arg1, ['foo','bar'])) {
}
if (in_array($arg1, array('foo','bar'))) {
}

$b = foo(
    "1", // this is a comment
    "2",  // this is a comment
    "3", // this is a comment
    "4"
);

var_dump(
    <<<TEXT
foo
TEXT
    ,
    'bar'
);

unset($foo, $bar);

$closure($foo, $bar);
$var = $closure() + $closure($foo, $bar) + self::$closure($foo, $bar);

class Test
{
    public static function baz($foo, $bar)
    {
        $a = new self($foo, $bar);
        $b = new static($foo, $bar);
    }
}

$obj->{$var}($foo, $bar);

(function ($a, $b) {
    return function ($c, $d) use ($a, $b) {
        echo $a, $b, $c, $d;
    };
})('a', 'b')('c', 'd');

my_function_call(
    'a',
    /* Comment */
    'b',
    'c', // Comment.
    'd',
    'e', // phpcs:ignore Standard.Category.Sniff -- for reasons.
    'f'
);

$foobar = php73_function_call_trailing_comma(
    $foo,
    $bar,
);

$foobar = functionCallAnonClassParam(
    new class() {
        public $foo=1;
        public function methodName($param='foo',$paramTwo='bar')
        {
            $bar=false;
            $foo = array(1,2,3);
        }
    },
    $args=array(),
);

$result = myFunction(param1: $arg1, param2: $arg2);
$result = myFunction(param1: $arg1,  param2:$arg2);
$result = myFunction(param1: $arg1, param2:$arg2, param3: $arg3, param4:$arg4, param5:$arg5);

class Testing extends Bar
{
    public static function baz($foo, $bar)
    {
        $a = new parent($foo, $bar);
        $a = new parent($foo, $bar);
    }
}

// Ignore spacing after PHP 7.3+ trailing comma in single-line function calls to prevent fixer conflicts.
// This is something which should be decided by a sniff dealing with the function call parentheses.
$foo = new MyClass($obj, 'getMethod',);
$foo = new MyClass($obj, 'getMethod',);
$foo = new MyClass($obj, 'getMethod',);
$foo = new MyClass(
    $obj,
    'getMethod',
);

#[AttributeName(1, 2)]
#[AttributeName(1, 2)]

$callable = myCallable(...);

// Skip over PHP 7.4 arrow functions.
// While any commas belonging to the code within the arrow function would always need to be within parentheses
// or within a short array, so there aren't any false positives, the sniff also does not need to examine these,
// so will be more efficient skipping over arrow functions.
$foobar = functionCallFnParamA(
    fn ($foo,$bar) => [1,2,3],
    $args,
);

$foobar = functionCallFnParamB(fn ($foo,$bar) => [1,2,3], $args);
$foobar = functionCallFnParamC($args, fn ($foo,$bar) => [1,2,3],);

// Ignore spacing within PHP 8.0 match control structures, which may have their own rules.
$foobar = functionCallMatchParam(
    match($foo) {
        1,2,3 => 'something',4,5,6 => 'else',default => 'works'
    }, // But check the spacing again once the match expression has finished.
    $args
);
