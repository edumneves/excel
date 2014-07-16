<?php
/**
 * Created by PhpStorm.
 * User: Edu Neves
 * Date: 11/02/14
 * Time: 22:11
 */

$nomeRel = "relatorio_012014";

$handle = fopen("./Entrada/" . $nomeRel . ".txt", "r");

if ($handle) {
    $csvRelatorio = fopen("./Saida/" . $nomeRel . ".csv", "w");
    echo "Abri relatorio <br>";
    $quantCamisas = 0;
    $quantAcessorios = 0;
    while (($line = fgets($handle)) !== false) {
        // process the line read.
        $vetorItem = explode(" ", $line);

//        echo "Li linha --> " . $line . "<br>";

        $vetorItem[0] = trim($vetorItem[0]);
        $tamItem = strlen($vetorItem[0]);
        $tipoItem = substr($vetorItem[0], 0, 2);

//        echo "<pre>     " . $vetorItem[0] . " tamanho = " . $tamItem . " Tipo item = " . $tipoItem . "</pre><br>";
        if (($tamItem == 10 || $tamItem == 11 ||$tamItem == 12) &&
            ($tipoItem == 'CM' || $tipoItem == 'AC')
        ){
//            echo "<pre>         ENTREI</pre><br>";
            $item = $vetorItem[0];

            // ignorar linha do fornecedor
            fgets($handle);

            $lineVenda = fgets($handle);
            $vetorVenda = explode(" ", $lineVenda);

            $vetorQuantidade = explode(",", $vetorVenda[0]);
            $quantVenda =$vetorQuantidade[0];
            fwrite($csvRelatorio, $item . ";" . $quantVenda . "\n");
  //          echo $item . ";" . $quantVenda . "<br>";

            if ($tipoItem == 'CM')
                $quantCamisas = $quantCamisas + $quantVenda;
            if ($tipoItem == 'AC')
                $quantAcessorios = $quantAcessorios+ $quantVenda;
        }
    }
    echo "<br><br> Quantidade de itens processados: <br> Camisas = " . $quantCamisas .
        "<br> Acessorios = " . $quantAcessorios . "<br> Total = " . ($quantCamisas + $quantAcessorios) . "<br><br>";
    fclose($csvRelatorio);
} else {
    // error opening the file.
}
