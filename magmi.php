<?php
/**
 * Created by PhpStorm.
 * User: edumneves
 * Date: 15/07/14
 * Time: 09:01
 */

$host = 'localhost:/Users/edumneves/REM.FDB';
$username = 'sysdba';
$password = 'masterkey';

$dbh = ibase_connect($host, $username, $password);
$stmt = 'SELECT * FROM PRODUTO';
$sth = ibase_query($dbh, $stmt);
echo "teste " . $sth;
while ($row = ibase_fetch_object($sth)) {
//    echo $row->DESCRICAO, "\n";
}
ibase_free_result($sth);
ibase_close($dbh);

?>