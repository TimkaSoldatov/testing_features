<?php
/**
1. ISERT INTO VALUES (man, 39), 
2. Dobrodeev Valera Vadimovich -> Dobrodeev V. V.
3. 10.01.1991 -> 10.01.91
4. 10/01/91 -> 10.01.91
5. parsing .html, sites Guzzle http, .js, .py
6. parsing .log, .json, .xml
7. regular expressions, A-parse
*/
//решение пункта 3
echo date('d.m.y', strtotime('10.01.1991'));
echo '<br>';
//решение пункта 4
$birthdayDate = explode('/', '10/01/91');
if(count($birthdayDate) == 3){
    echo date('d.m.y', strtotime($birthdayDate[0].'.'.$birthdayDate[1].'.'.$birthdayDate[2]));
}
$myName = 'Dobrodeev Valera Vadimovich';
$explodeMyName = explode(' ',$myName);
