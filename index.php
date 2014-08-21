<?php
/*
Script Name: Read excel file in php with example
*/
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>
<body>


<?php
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');

/** PHPExcel_IOFactory */
include_once 'PHPExcel/IOFactory.php';
include_once 'ItemEstoque.php';
include_once 'Camisa.php';
include_once 'ItemFactory.php';
include_once 'Catalogo.php';
include_once 'Conf.php';

//$inputFileName = './relatorio estoque.xls';  // File to read
//$inputFileName = './Estoque_20140625_modificada.xls';  // File to read
//$inputFileName = './Entrada/entrada.xls';  // File to read
//$inputFileName = './Entrada/entrada_julho.xls';  // File to read
$inputFileName = './Entrada/rel_estoque_20140814.xlsx'; // File to read

global $host;
global $username;
global $password;


$dbh = ibase_connect($host, $username, $password);
$stmt = "select
  p1.codbarra,
  p1.saldo as saldo_esc,
  p2.saldo as saldo_loja
from
  produto p1 JOIN
  produto p2 on (p1.CODIGO = p2.codigo)
WHERE p1.empresa = '01' and p2.empresa = '02'
order BY p1.codbarra";
$sth = ibase_query($dbh, $stmt);

$listaBD = array();
while ($row = ibase_fetch_object($sth)) {
    $codBarra = trim($row->CODBARRA);
    $saldoEsc = (int)trim($row->SALDO_ESC);
    $saldoLoja = (int)trim($row->SALDO_LOJA);

    $listaBD[$codBarra]['ESC'] = $saldoEsc;
    $listaBD[$codBarra]['LOJA'] = $saldoLoja;
}


ibase_free_result($sth);
ibase_close($dbh);

//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
try {
    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch (Exception $e) {
    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
}


echo '<hr />';
echo "<pre>";

$today = getdate();
error_log("Importação de " . $today['mday'] . "/" . $today['mon'] . "/" . $today['year']);
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

$catalogoFisico = new Catalogo;
$catalogoFisico->montaCatalogo($sheetData, $listaBD);




?>
<body>
</html>