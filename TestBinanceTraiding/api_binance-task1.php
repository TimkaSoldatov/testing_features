:<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Последняя компиляция и сжатый CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Дополнение к теме -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</head>
<body>
<!-- Последняя компиляция и сжатый JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<?php ?>
<?php
include( 'config.php' );
$query = 'INSERT INTO `currencies`(`current_cur`, `value`) VALUES (`ETHBTC`,0.03406600), (`LTCBTC`,0.00466300)';
$stmt  = $pdo->query( $query );

echo '<table class="table table-dark">';
echo '<thead>
    <tr>
        <th scope="col">currency</th>
        <th scope="col">course</th>   
    </tr>
    </thead>
    <tbody>';
while ( $row = $stmt->fetch( PDO::FETCH_LAZY ) ) {
	$query12 = 'SELECT * FROM `gt1_comments` WHERE `user_id`=' . intval( $row['user_id'] ) . " ORDER BY `date` DESC LIMIT 1";
	$stmt2   = $pdo->query( $query12 );
	$row2    = $stmt2->fetch( PDO::FETCH_LAZY );
	echo '<tr><td>' . $row['user_id'] . '</td><td>' . $row['fio'] . '</td><td>' . $row2['comments'] . '</td><td>' . $row2['date'] . '</td></tr>';
}
echo '</tbody>
</table>';
?>
</body>
</html>