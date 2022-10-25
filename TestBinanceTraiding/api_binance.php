<?php
ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link type="text/css" rel="stylesheet"
          href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"></link>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <!-- CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <!-- JS, Popper.js, and jQuery -->
    <!--    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->
    <!--    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>
</head>
<body>
<form action="api_binance.php" method="post">
    <div class="form-group">
        <label for="exampleInputEmail1">Валюта</label>
        <input type="text" class="form-control" id="exampleInputEmail1" required="required" aria-describedby="emailHelp"
               name="currency">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Курс валюты</label>
        <input type="number" class="form-control" required="required" id="exampleInputPassword1" step="any" name="rate">
    </div>
    <!--<div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Check me out</label>
    </div>-->
    <button type="submit" name="toDB" class="btn btn-primary">Добавить в БД</button>
    <button type="submit" name="toFile" class="btn btn-primary">Добавить в json</button>
</form>

<?php
$res_binance = array();
$curl        = curl_init();
/*curl_setopt($curl, CURLOPT_URL, 'https://api.binance.com/api/v3/exchangeInfo');*/
// Получаем курсы криптовалют из binance
curl_setopt( $curl, CURLOPT_URL, 'https://api.binance.com/api/v3/ticker/price' );

curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
$res_binance = curl_exec( $curl );
$res_binance = json_decode( $res_binance, true );
/*echo '<pre>';
print_r( $res_binance );
echo '</pre>';*/
//$res_binance = $res_binance['symbols'];
curl_close( $curl );
// сохраняем в БД
if ( isset( $_REQUEST['toDB'] ) ) {
	$currency = isset( $_REQUEST['currency'] ) ? addslashes( $_REQUEST['currency'] ) : '';
	$rate     = isset( $_REQUEST['rate'] ) ? floatval( $_REQUEST['rate'] ) : 0;
	if ( $currency != '' && $rate != 0 ) {
		$queryInsert = "INSERT IGNORE INTO `currencies` (`currency_pair`, `value`) VALUES ('$currency', $rate)";
		$res         = $pdo->query( $queryInsert );
	}
}
// сохраняем в файл
if ( isset( $_REQUEST['toFile'] ) ) {
	$currency = isset( $_REQUEST['currency'] ) ? addslashes( $_REQUEST['currency'] ) : '';
	$rate     = isset( $_REQUEST['rate'] ) ? floatval( $_REQUEST['rate'] ) : 0;
	if ( $currency != '' && $rate != 0 ) {
		$output                      = file_get_contents( 'courses.json' );
		$outputFromFile              = json_decode( $output, true );
		$outputFromFile["$currency"] = "$rate";// или добавляем, или изменяем
		$outputFromFile              = json_encode( $outputFromFile );
		file_put_contents( 'courses.json', $outputFromFile );
	}
}

$output         = file_get_contents( 'courses.json' );// получаем содержимое файла courses.json
$outputFromFile = json_decode( $output, true );
/*$outputFromFile2 = json_decode($output);
echo "<pre>";
print_r($outputFromFile['eee']);
echo '</pre>';
echo "<pre>";
print_r($outputFromFile2->eee);
echo '</pre>';*/
$out = json_decode( $output, true );
//IGNORE если есть BTC -> гривна, то не вставляем
$query = 'INSERT IGNORE INTO `currencies` (`currency_pair`, `value`) VALUES ';

/**
 * Нужно сформировать запрос для вставки курса криптовалют в таблицу, после последней валюты в запросе не
 * должно быть запятой:
 * первый способ как это сделать: ввести счетчик $i, который будет ждать пока не приступим к последней валюте
 * и тогда запятую не будем писать
 * второй способ: foreach(implode()) делаем массив, потом implode()
 */
foreach ( $out as $key => $value ) {
	if ( $value == end($out) ) {
		$query .= "('$key', $value) ";
	} else {
		$query .= "('$key', $value), ";
	}
}
// вариант 3
$mas_query = [];
foreach ( $out as $key => $value ) {
    $mas_query[] = "('$key', $value) ";
}
if(!empty($mas_query)){
    $query = implode(',', $mas_query);
}
// выполнить запрос PDO $pdo->query($query)
?>
<?php
// Получаем курсы криптовалют из БД
$sth = $pdo->prepare( "SELECT * FROM `currencies`" );
$sth->execute();
$result = $sth->fetchAll();
?>

<table id="example" class="display" style="width:100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>Currency pairs</th>
        <th>Course</th>
    </tr>
    </thead>
    <tbody>
	<?php if ( ! empty( $result ) ) { ?>
		<?php foreach ( $result as $res ) { ?>
            <tr>
				<?php
				// красим BTC в green, ETH в yellow, USDT в blue
				$background = '';
				if ( substr( $res['currency_pair'], - 3 ) == "BTC" ) {
					$background = 'style="background:green"';
				} elseif ( substr( $res['currency_pair'], - 3 ) == "ETH" ) {
					$background = 'style="background:yellow"';
				} elseif ( substr( $res['currency_pair'], - 4 ) == "USDT" ) {
					$background = 'style="background:blue"';
				}
				?>
                <td <?php echo $background; ?>><?php echo $res['id']; ?></td>
                <td <?php echo $background; ?>><?php echo $res['currency_pair']; ?></td>
                <td <?php echo $background; ?>><?php echo $res['value']; ?></td>
            </tr>
		<?php } ?>
	<?php } else { ?>
        <tr>
            <td colspan="3">Empty data</td>
        </tr>
	<?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <th>ID</th>
        <th>Currency pairs</th>
        <th>Course</th>
    </tr>
    </tfoot>
</table>

<table id="exampleReal" class="display" style="width:100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>Currency pairs</th>
        <th>Course</th>
    </tr>
    </thead>
    <tbody>
	<?php if ( ! empty( $res_binance ) ) {
		$i = 0; ?>
		<?php foreach ( $res_binance as $res ) { ?>
            <tr>
                <td><?php echo $i ++; ?></td>
                <td><?php echo $res['symbol']; ?></td>
                <td><?php echo $res['price']; ?></td>
            </tr>
		<?php } ?>
	<?php } else { ?>
        <tr>
            <td colspan="3">Empty data</td>
        </tr>
	<?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <th>Status</th>
        <th>Currency pairs</th>
        <th>Course</th>
    </tr>
    </tfoot>
</table>

<script type="text/javascript">
    $(document).ready(function () {
        $('#example').DataTable();
    });
    $(document).ready(function () {
        $('#exampleReal').DataTable();
    });
</script>

</body>
</html>