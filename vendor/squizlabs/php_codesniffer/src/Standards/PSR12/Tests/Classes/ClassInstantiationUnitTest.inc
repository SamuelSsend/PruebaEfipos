<?php
$foo = new Foo();
$foo = new Foo;
$foo = new Foo\Bar;
$foo = new Foo\Bar();
$foo = new Foo();

$foo = new $foo();
$foo = new $foo;
$foo = new $$foo();
$foo = new $$foo;

$foo = new self();
$foo = new self;
$foo = new static();
$foo = new static;

foo(
    new class {
    }
);
echo (new Foo())->bar();
echo (new Foo)->bar();
echo (new Foo((new Bar)->getBaz()))->bar();
$foo = (new Foo)::$bar;

echo (new Foo(
    (new Bar//comment
    )->getBaz(new Baz /* comment */)
))->bar();

$foo = new $bar['a']();
$foo = new $bar['a']['b']();
$foo = new $bar['a'][$baz['a']['b']]['b']();
$foo = new $bar['a'] [$baz['a']/* comment */  ['b']]['b'];

$a = new self::$transport[$cap_string];
$renderer = new $this->inline_diff_renderer;
$a = new ${$varHoldingClassName};

$class = new $obj?->classname();
$class = new $obj?->classname;
$class = new ${$obj?->classname};

// Issue 3456.
// Anon classes should be skipped, even when there is an attribute between the new and the class keywords.
$anonWithAttribute = new #[SomeAttribute('summary')] class {
    public const SOME_STUFF = 'foo';
};

$foo = new parent();
$foo = new parent;

// PHP 8.3: safeguard that the sniff ignores anonymous classes, even when declared as readonly.
$anon = new readonly class {
};
$anon = new #[MyAttribute] readonly class {
};
