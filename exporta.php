<?php
/**
 * Created by PhpStorm.
 * User: edumneves
 * Date: 20/08/14
 * Time: 20:50
 */

include_once 'Classes/Conf.php';

global $host;
global $username;
global $password;

$dbh = ibase_connect($host, $username, $password);

echo "Banco de dados = " . $host . "\r\n";
echo "User name = " . $username . "\r\n";
echo "Senha = " . $password . "\r\n";

$stmt = "select
  codigo as SKU,
  case when saldo < 0 then 0 else saldo end as QTY
from produto
where empresa = '01'
order by saldo desc";
$sth = ibase_query($dbh, $stmt);

$caminho = "./Saida_site/update_stock.csv";

// Cabeçalho para o arquivo de update
$csv = fopen($caminho, "w");
$header = array();
$header[] = "sku";
$header[] = "qty";
$header[] = "min_qty";
fputcsv($csv, $header, ";");

$quant = 0;

// Gera lista de produtos com o estoque
$listaBD = array();
while ($row = ibase_fetch_object($sth)) {
    $quant++;

    $codBarra = trim($row->SKU);
    $saldoEsc = (int)trim($row->QTY);
    $minQty = 2;

    $produto = array(
        $codBarra,
        $saldoEsc,
        $minQty
    );
    fputcsv($csv, $produto, ";");
}
fclose($csv);

echo $quant . " produtos processados.\n\r";

ibase_free_result($sth);
ibase_close($dbh);

