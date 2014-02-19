<?php
const IND_COD 			= 'A';
const IND_COD_BARRA 	= 'B';
const IND_DESC 			= 'C';
const IND_COD_FORN 		= 'D';
const IND_REF_FORN 		= 'E';
const IND_PREC_CUSTO 	= 'F';
const IND_PREC_VENDA 	= 'G';
const IND_SALDO 		= 'H';
const IND_SALDO_PESO 	= 'I';

const PRECO_VENDA = 25;
const PRECO_CUSTO = 15;


$tamanhos = array ("PP", "P", "M", "G", "GG", "XGG");

$arqListaBandasJSON = "./Conf/listaBandas.json";
$arqListaAcertosJSON = "./Conf/listaAcertos.json";
$arqListaSubstituicoesJSON = "./Conf/listaSubstituicoes.json";

$listaBandas = $json = json_decode(file_get_contents($arqListaBandasJSON), true);
$listaAcertos = $json = json_decode(file_get_contents($arqListaAcertosJSON), true);
$listaSubstituicoes = $json = json_decode(file_get_contents($arqListaSubstituicoesJSON), true);

ksort($listaBandas);

$listaChaves = array_keys($listaBandas);

print_r($listaChaves);

function binary_search(array $a, $first, $last, $key){
    $lo = $first;
    $hi = $last - 1;

    while ($lo <= $hi) {
        $mid = (int)(($hi - $lo) / 2) + $lo;
        $cmp = strcmp($a[$mid], $key);

        if ($cmp < 0) {
            $lo = $mid + 1;
        } elseif ($cmp > 0) {
            $hi = $mid - 1;
        } else {
            return $mid;
        }
    }
    return -($lo + 1);

}

class ImportacaoGlobal {

    public static function getTipoItem(array $item){
        $tipoItem = substr(ImportacaoGlobal::extraiCodigo($item), 0, 2);
        switch ($tipoItem) {
            case "AC":
                return "Acessorio";
            case "CM":
                return "Camisa";
        }
        return null;
    }

    public static function extraiCodigo(array $item){
        $texto = trim($item[IND_COD]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiCodigoBarra(array $item){
        $texto = trim($item[IND_COD_BARRA]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiDescricao(array $item){
        global $listaSubstituicoes;
        global $listaAcertos;

        $texto = trim($item[IND_DESC]);
        $texto = preg_replace( '/\s+/', ' ', $texto);

        $codigo = ImportacaoGlobal::extraiCodigo($item);

        //Faz replace dos acertos de texto
        foreach($listaAcertos as $chave => $valor){
            if ($chave == $codigo){
                $texto = $valor;
            }
        }


        //Faz replace dos acertos de texto
        foreach($listaSubstituicoes as $chave => $valor){
            $texto = str_replace($chave, $valor, $texto);
        }

        return ($texto);
    }
    public static function extraiCodFornecedor(array $item){
        $texto = trim($item[IND_COD_FORN]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiRefFornecedor(array $item){
        $texto = trim($item[IND_REF_FORN]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiPrecoCusto(array $item){
        $texto = trim($item[IND_PREC_CUSTO]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiPrecoVenda(array $item){
        $texto = trim($item[IND_PREC_VENDA]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiSaldo(array $item){
        $texto = trim($item[IND_SALDO]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
    public static function extraiSaldoPeso(array $item){
        $texto = trim($item[IND_SALDO_PESO]);
        $texto = preg_replace( '/\s+/', ' ', $texto);
        return ($texto);
    }
}

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
*/
?>