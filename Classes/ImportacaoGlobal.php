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

$arqListaBandasJSON = "./Conf/listaBandas.json";

$listaBandas = $json = json_decode(file_get_contents($arqListaBandasJSON), true);

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
        return (trim($item[IND_COD]));
    }
    public static function extraiCodigoBarra(array $item){
        return (trim($item[IND_COD_BARRA]));
    }
    public static function extraiDescricao(array $item){
        return (trim($item[IND_DESC]));
    }
    public static function extraiCodFornecedor(array $item){
        return (trim($item[IND_COD_FORN]));
    }
    public static function extraiRefFornecedor(array $item){
        return (trim($item[IND_REF_FORN]));
    }
    public static function extraiPrecoCusto(array $item){
        return (trim($item[IND_PREC_CUSTO]));
    }
    public static function extraiPrecoVenda(array $item){
        return (trim($item[IND_PREC_VENDA]));
    }
    public static function extraiSaldo(array $item){
        return (trim($item[IND_SALDO]));
    }
    public static function extraiSaldoPeso(array $item){
        return (trim($item[IND_SALDO_PESO]));
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