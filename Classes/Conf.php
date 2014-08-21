<?php
/**
 * Created by PhpStorm.
 * User: edumneves
 * Date: 20/08/14
 * Time: 21:10
 */

// Arquivo de configuração
$ini_array = parse_ini_file("conf/excel.ini", true);

//Configuração de Banco de Dados
$host       = $ini_array["BANCO_DADOS"]["HOST"];
$username   = $ini_array["BANCO_DADOS"]["USERNAME"];
$password   = $ini_array["BANCO_DADOS"]["PASSWORD"];

// Configuração utilizada no relatório de estoque, quantidade mínima para mostrar a camisa no relatório
$rel_quant_min = array();
$rel_quant_min['BL'] = $ini_array["REL_QUANT_MIN"]["BL"];
$rel_quant_min['CM'] = $ini_array["REL_QUANT_MIN"]["CM"];
$rel_quant_min['BY'] = $ini_array["REL_QUANT_MIN"]["BY"];
$rel_quant_min['IN'] = $ini_array["REL_QUANT_MIN"]["IN"];
$rel_quant_min['MC'] = $ini_array["REL_QUANT_MIN"]["MC"];
