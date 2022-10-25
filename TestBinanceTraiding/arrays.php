<?php
declare( strict_types=1 );
// массив курсов криптовалют
$array  = [ 'etc' => 0.03453, 'eth' => 0.5435, 'sibcoin' => 0.3232, 'dogcoin' => 0.034, 'altcoin' => 1.0433 ];
$parts  = [ 'apple', 'pear' ];
//$fruits = [ 'banana', 'orange', ...$parts, 'watermelon' ];
/*echo '<pre>';
print_r( $array );
echo '</pre>';
$implode = implode( ',', $array );
echo $implode;
echo '<br>';
*/
require __DIR__ . '/vendor/autoload.php';
//include 'C:\OSPanel\domains\wfpsports.top';

// create a variable, which could be anything!
$someVar = $array;

dump( $someVar );

// dump() returns the passed value, so you can dump an object and keep using it
//dump($someObject)->someMethod();


/**
 * Подготавливает запрос для вставки, в конце запрса не должно быть запятой
 *
 * @param  array  $array
 *
 * @return string
 */
function prepareInsertQuery( array $array ): string {
	$prepareQuery = [];
	foreach ( $array as $item => $value ) {
		$prepareQuery[] = "('" . $item . "', " . $value . '), ';
	}
	echo implode( $prepareQuery );
	echo '<br>';
	$rest = substr( implode( $prepareQuery ), 0, - 2 );

	return $rest;
}

echo prepareInsertQuery( $array );