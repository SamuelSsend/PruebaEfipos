<?php
function getVar($varname='var', $var='var')
{
    $var = $var2 = getVar();
    $var = $var2 = $var3 = '';

    if ($varname === 'var' || $var2 === 'varname' || $var = true) {
        $var[($var + 1)] = $var2;
        $var[($var = 1)] = $var2;
    }

    for ($i = $var; (null !== ($key = key($i))); next($i)) {
        while ($i = true) {
            echo $i = getVar();
            echo \A\B\C::$d = getVar();
        }
    }

    while ($row = $query->fetch(PDO::FETCH_NUM)) {
        $result[$row[0]] = array();
        $result[$row[0]][] = $current;

        self::$_inTransaction = true;
        parent::$_inTransaction = true;
        static::$_inTransaction = true;
        $$varName = $varValue;
    }

}//end getVar()

class myClass
{
    private static $_dbh = null;
    public $dbh = null;
    protected $dbh = null;
    var $dbh = null; // Old PHP4 compatible code.
}

A::$a = 'b';
\A::$a = 'c';
\A\B\C::$d = 'd';
B\C::$d = 'e';

@$a = 1;

$a = [];
foreach ($a as $b) {
    $c = 'd';
}

$var = $var2;
list ($a, $b) = explode(',', $c);
$var1 ? $var2 = 0 : $var2 = 1;

$obj->$classVar = $prefix.'-'.$type;

$closureWithDefaultParamter = function (array $testArray=array()) {
};
?>
<?php $var = false; ?>

<?php

while ( ( $csvdata = fgetcsv($handle, 2000, $separator) ) !== false ) {
    // Do something.
}

// Issue #2787.
class Foo
{
    protected ?int $id = null;
}

class Bar
{
    // Multi-property assignments.
    private $a = false, $b = true; // Non-typed.
    public bool $c = false, $d = true;
    protected int $e = 123, $f = 987;
}

switch ($b < 10 && $a = 10) {
case true:
    break;
}

$array = [
    match ($b < 10 && $a = 10) {
        true => 10,
        false => 0
    },
];

$arrow_function = fn ($a = null) => $a;

function ($html) {
    $regEx = '/regexp/';

    return preg_replace_callback(
        $regEx, function ($matches) {
            [$all] = $matches;
            return $all;
        }, $html
    );
};


function () {
    $a = false;

    some_label:

    $b = getB();
};

?>
<?php echo $var = false; ?>

// Issue PHPCSStandards/PHP_CodeSniffer#537.
<?php $a * $b ?>
<?php
list ($c, $d) = explode(',', '1,2');
?>
<?php echo $a * $b ?>
<?php
list ($c, $d) = explode(',', '1,2');
?>
<?php echo $a * $b ?>
<?php echo list ($c, $d) = explode(',', '1,2');
?>
<?php $a * $b ?>
<?php
[$c, $d] = explode(',', '1,2');
?>
<?php echo $a * $b ?>
<?php
[$c, $d] = explode(',', '1,2');
?>
<?php echo $a * $b ?>
<?php echo [$c, $d] = explode(',', '1,2');
?>
<?php

// Issue #598.
$filtered_results->field = $result;
$filtered_results->$field = $result;
$filtered_results->$row->field = $result;
$filtered_results->$row->$field = $result;

$filtered_results[ $i ]->field = $result;
$filtered_results[ $i ]->$field = $result;
$filtered_results[ $i ]->$row->field = $result;
$filtered_results[ $i ]->$row->$field = $result;
$filtered_results[ $i ]->$row[0]->field = $result;
$filtered_results[ $i ]->$row[$j]->$field = $result;
