<?php
	/*
Script Name: Read excel file in php with example
Script URI: http://allitstuff.com/?p=1303
Website URI: http://allitstuff.com/
*/
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<a href="http://allitstuff.com/">AllItStuff.com</a>



<?php
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');

/** PHPExcel_IOFactory */
include_once 'PHPExcel/IOFactory.php';
include_once 'ItemEstoque.php';
include_once 'Camisa.php';
include_once 'ItemFactory.php';
include_once 'Catalogo.php';

//$inputFileName = './relatorio estoque.xls';  // File to read
//$inputFileName = './Estoque_20140625_modificada.xls';  // File to read
$inputFileName = './Entrada/entrada.xls';  // File to read

//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}


echo '<hr />';
echo "<pre>";

$today = getdate();
error_log("Importação de " . $today['mday'] . "/" . $today['mon'] . "/" .$today['year']);
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

$catalogoFisico = new Catalogo;
$catalogoFisico->montaCatalogo($sheetData);


/*
 *
    [1] => Array
    (
        [A] => CODIGO
        [B] => CODIGO BARRA
        [C] => DESCRICAO
        [D] => COD.FORNECEDOR
        [E] => REF.FORNECEDOR
        [F] => PRECO CUSTO
        [G] => PRECO VENDA
        [H] => SALDO
        [I] => SALDO PESO
    )

    [2] => Array
    (
        [A] => ACIMASM00001
        [B] => 52741
        [C] => ACESSORIOS IMAS MEDIOS BANDAS
        [D] =>
        [E] =>
        [F] => 3
        [G] => 6
        [H] => 0
        [I] => 0
    )
    
	
 *
 *
 */

?>
<body>
</html>