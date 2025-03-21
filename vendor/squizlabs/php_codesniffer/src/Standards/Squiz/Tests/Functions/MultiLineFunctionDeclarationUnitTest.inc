<?php
public function someFunctionWithAVeryLongName($firstParameter='something',
    $secondParameter='booooo',
    $third=null, $fourthParameter=false,
    $fifthParameter=123.12,
    $sixthParam=true
) {
}

function someFunctionWithAVeryLongName2(
    $firstParameter='something',
    $secondParameter='booooo',
) {
}

function blah()
{
}

function blah()
{
}

class MyClass
{

    public function someFunctionWithAVeryLongName(
        $firstParameter='something',
        $secondParameter='booooo',
        $third=null,
        $fourthParameter=false,
        $fifthParameter=123.12,
        $sixthParam=true
    ) { /**
 * w00t 
*/ 
    }

    public function someFunctionWithAVeryLongName2($firstParameter='something',
        $secondParameter='booooo',
        $third=null
    ) {
    }

    public function someFunctionWithAVeryLongName3(
        $firstParameter, $secondParameter, $third=null
    ) {
    }

    public function someFunctionWithAVeryLongName4(
        $firstParameter, $secondParameter
    ) {
    }

    public function someFunctionWithAVeryLongName5(
        $firstParameter,
        $secondParameter=array(1,2,3),
        $third=null
    ) {
    }

}

$noArgs_longVars = function () use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function ($longArgument,
    $muchLongerArgument
) use ($muchLongerVar3
) {
    // body
};

$noArgs_longVars = function () use (
    $longVar1,  $longerVar2,
    $muchLongerVar3
) {
    // body
};

usort(
    $data,
    function ($a, $b) {
        // body
    }
);

function myFunction(
    $firstParameter,$secondParameter=[1,2,3],$third=null
) {
}

if (array_filter(
    $commands,
    function ($cmd) use ($commandName) {
        return ($cmd['name'] == $commandName);
    }
)
) {
    // Do something
}

function foo( // comment
    $bar,
    $baz
) { // comment
    // ...
}

function foo($bar = [
    1,
    2,
], $foo
) {
    // body
}

$foo = function ($bar = [
    1,
    2,
]
) use ($longVar1, $longerVar2) {
    // body
};

function foo($bar = [
    1,
    2,
],
    $foo
) {
    // body
}

function foo(
    $bar = [
        1,
        2,
    ],
    $foo
) {
    // body
}

function foo(
    $param1,
    $param2,
    $param3,
) : SomeClass {
}

// Issue 1959.
function __construct(
    $foo,   // This is foo
    $bar
) {
}

function __construct(
    $foo /* this is foo */,
    $bar // this is bar
) {
}

function __construct(
    $foo,   // phpcs:ignore Standard.Category.Sniff -- for reasons.
    $bar
) {
}

public function hello(string $greeting
) {
}

public function hello(string $greeting // cant fix this
) {
}

public function hello(string $greeting // phpcs:ignore Standard.Category.Sniff -- for reasons.
) {
}

function foo(
    $foo,
    $bar
) {
}

class ConstructorPropertyPromotionSingleLineDocblockIndentOK
{
    public function __construct(
        /**
         * @var string 
         */
        public string $public,
        /**
         * @var string 
         */
        private string $private,
    ) {
    }
}

class ConstructorPropertyPromotionMultiLineDocblockAndAttributeIndentOK
{
    public function __construct(
        /**
         * @var               string
         * @Assert\NotBlank()
         */
        public string $public,
        /**
         * @var               string
         * @Assert\NotBlank()
         */
        #[NotBlank]
        private string $private,
    ) {
    }
}

class ConstructorPropertyPromotionSingleLineDocblockIncorrectIndent
{
    public function __construct(
        /**
         * @var string 
         */
        public string $public,
        /**
         * @var string 
         */
        private string $private,
    ) {
    }
}

class ConstructorPropertyPromotionMultiLineDocblockAndAttributeIncorrectIndent
{
    public function __construct(
        /**
         * @var               string
         * @Assert\NotBlank()
         */
        public string $public,
        /**
         * @var               string
         * @Assert\NotBlank()
         */
        #[NotBlank]
        private string $private,
    ) {
    }
}

// PHP 8.1: new in initializers means that class instantiations with parameters can occur in a function declaration.
function usingNewInInitializersCallParamsIndented(
    int $paramA,
    string $paramB,
    object $paramC = new SomeClass(
        new InjectedDependencyA(),
        new InjectedDependencyB
    )
) {
}

function usingNewInInitializersCallParamsNotIndented(
    int $paramA,
    string $paramB,
    object $paramC = new SomeClass(
        new InjectedDependencyA,
        new InjectedDependencyB()
    )
) {
}

function usingNewInInitializersCallParamsIncorrectlyIndentedShouldNotBeFlaggedNorFixed(
    int $paramA,
    string $paramB,
    object $paramC = new SomeClass(
        new InjectedDependencyA(), new InjectedDependencyB()
)
) {
}

class UsingNewInInitializers
{
    public function doSomething(
        object $paramA,
        stdClass $paramB = new stdClass(),
        Exception $paramC = new Exception(
            new ExceptionMessage(),
            new ExceptionCode(),
        ),
    ) {
    }

    public function callParamsIncorrectlyIndentedShouldNotBeFlaggedNorFixed(
        Exception $param = new Exception(
            new ExceptionMessage(),
            new ExceptionCode(),
        ),
    ) {
    }
}

// Issue #608 - multi-attributes are not handled correctly.
function ParamWithMultiAttributeOnSameLine(
    #[AttributeA, AttributeB] string $param,
) {
}

function ParamWithMultiAttributeOnSameLineWithParamsShouldNotBeSeenAsMultipleFnParams(
    #[AttributeA(10, 'test'), AttributeB([1, 2, 3,])] string $param,
) {
}

function ParamWithMultiAttributeOnSameLine(
    #[AttributeA, AttributeB] string $paramA, int $paramB
) {
}

function ParamWithMultiAttributeOnSameLineWithParamsShouldNotBeSeenAsMultipleFnParams(
    #[AttributeA(10, 'test'), AttributeB([1, 2, 3,])] string $param, int $paramB
) {
}

function ParamWithAttributeOnOwnLineShouldNotBeSeenAsMultipleFnParams(
    #[Attribute]
    string $param,
) {
}

function ParamWithMultipleAttributesOnOwnLineShouldNotBeSeenAsMultipleFnParams(
    #[AttributeA]
    #[AttributeB]
    string $param,
) {
}

function ParamWithAttributeOnOwnLineWithParamsShouldNotBeSeenAsMultipleFnParamse(
    #[Attribute(10, 20)]
    string $param,
) {
}

function ParamWithMultiAttributeOnOwnLineShouldNotBeSeenAsMultipleFnParams(
    #[AttributeA, AttributeB]
    string $param,
) {
}

function ParamWithMultiAttributeOnOwnLineWithParamsShouldNotBeSeenAsMultipleFnParams(
    #[AttributeA(10, 'test'), AttributeB([1, 2, 3,])]
    string $param,
) {
}
