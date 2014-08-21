<?php
/**
 * Created by PhpStorm.
 * User: edumneves
 * Date: 13/08/14
 * Time: 21:14
 */

$ini_array = parse_ini_file("conf/excel.ini", true);

$host = $ini_array["BANCO_DADOS"]["HOST"];
$username = $ini_array["BANCO_DADOS"]["USERNAME"];
$password = $ini_array["BANCO_DADOS"]["PASSWORD"];

$dbh = ibase_connect($host, $username, $password);

$stmt = "SELECT * FROM (
  SELECT
    TABELA.CODIGO,
    TABELA.DESCRICAO,
    P2.COR AS TAMANHO,
    P2.NOMEFOR,
    P2.REFERENCIA_FOR,
    P2.SALDO                                                AS ESCRITORIO,
    (SELECT P1.SALDO
     FROM PRODUTO P1
     WHERE P1.CODIGO = TABELA.CODIGO AND P1.EMPRESA = '02') AS LOJA,
    CEILING(AVG(ULT_3_MESES))                               AS MEDIA_ULT_3_MESES,
    MAX(mes_atual) AS mes_atual
  FROM (

         SELECT
           EXTRACT(MONTH FROM DATA)                                                                      AS MES,
           p.codigo,
           p.descricao,
           SUM(CASE WHEN EXTRACT(MONTH FROM DATA) = EXTRACT(MONTH FROM
                                                            CURRENT_DATE) THEN 0 ELSE iv.quantidade END) AS ult_3_meses,
           SUM(CASE WHEN EXTRACT(MONTH FROM DATA) = EXTRACT(MONTH FROM
                                                            CURRENT_DATE) THEN iv.quantidade ELSE 0 END) AS mes_atual
         FROM
           ITENS_VENDA IV JOIN
           PRODUTO P ON (P.CODIGO = IV.CD_PRODUTO AND p.empresa = iv.empresa)
         WHERE
           iv.empresa = '02' AND
           EXTRACT(MONTH FROM DATA) >= EXTRACT(MONTH FROM CURRENT_DATE) - 4 AND
           EXTRACT(YEAR FROM DATA) = EXTRACT(YEAR FROM CURRENT_DATE)
           --AND P.CODBARRA = '052571'
               --AND P.CODIGO = 'CMCMM00160'
           AND P.TIPO_PECA = 'CM'
         GROUP BY
           EXTRACT(MONTH FROM DATA),
         p.codigo,
         p.descricao
       ) TABELA JOIN
    PRODUTO P2 ON (P2.CODIGO = TABELA.CODIGO AND P2.EMPRESA = '01')
  WHERE
    P2.CODIGO NOT IN ('CMBLG00085', 'CMCMG00423')
  GROUP BY
    TABELA.CODIGO,
    TABELA.DESCRICAO,
    P2.NOMEFOR,
    P2.REFERENCIA_FOR,
      P2.COR,
    P2.SALDO
) VENDAS
  WHERE
    VENDAS.ESCRITORIO + VENDAS.LOJA < (MEDIA_ULT_3_MESES )*1.5 OR
    VENDAS.ESCRITORIO + VENDAS.LOJA < (mes_atual)*1.5
ORDER BY 4, 5, 7 DESC,8 DESC;";

$sth = ibase_query($dbh, $stmt);

while ($row = ibase_fetch_object($sth)) {

    echo "Produto " .
        trim($row->CODIGO) . " " .
        trim($row->NOMEFOR) . " " .
        trim($row->DESCRICAO) . " " .
        trim($row->TAMANHO) . " " .
        trim($row->REFERENCIA_FOR) . " <br/> ";
}


ibase_free_result($sth);
ibase_close($dbh);

echo "<br>Gravei o excel<br>";