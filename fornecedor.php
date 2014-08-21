<?php
/**
 * Created by PhpStorm.
 * User: edumneves
 * Date: 13/08/14
 * Time: 21:14
 */
set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
include_once 'PHPExcel/IOFactory.php';

$nomeRel = "./Saida/Relatorio_Fornecedor_Camisa.xlsx";

function configuraDimensoesRelatorio($objPHPExcel)
{
    // Ajuste de tamanho das colunas
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->getColumnDimension('A')->setWidth(2);
    $sheet->getColumnDimension('B')->setWidth(2);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setWidth(5);
    $sheet->getColumnDimension('G')->setWidth(8);
    $sheet->getColumnDimension('H')->setWidth(8);
    $sheet->getColumnDimension('I')->setWidth(8);
    $sheet->getColumnDimension('J')->setWidth(8);

    $sheet->getStyle('A1:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $sheet->getStyle('D2:D' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E2:E' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('E2:E' . $objPHPExcel->getActiveSheet()->getHighestRow())
        ->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    // Coluna de input
    $sheet->getStyle("F2:F" . $sheet->getHighestRow())->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'e9e1a3')
            )
        )
    );

    // Coluna de estoque
    $sheet->getStyle("G2:H" . $sheet->getHighestRow())->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'dcf4f2')
            )
        )
    );

    // Coluna de vendas
    $sheet->getStyle("I2:J" . $sheet->getHighestRow())->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'efded0')
            )
        )
    );

    // Coluna de Sugestão
    $sheet->getStyle("K2:K" . $sheet->getHighestRow())->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'cef3ca')
            )
        )
    );

    // Cabeçalho
    $sheet->getStyle("A1:K2")->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'dbdbdb')
            )
        )
    );

    // Colunas de Sugestão e Pedido em negrito
    $styleArray = array(
        'font' => array(
            'bold' => true
        )
    );
    $sheet->getStyle("K2:K" . $sheet->getHighestRow())->applyFromArray($styleArray);
    $sheet->getStyle("F2:F" . $sheet->getHighestRow())->applyFromArray($styleArray);

    /** Borders for all data */
    $objPHPExcel->getActiveSheet()->getStyle(
        'A3:' .
        $objPHPExcel->getActiveSheet()->getHighestColumn() .
        $objPHPExcel->getActiveSheet()->getHighestRow()
    )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


    /** Borders for heading */
    $objPHPExcel->getActiveSheet()->getStyle(
        'A1:' .
        $objPHPExcel->getActiveSheet()->getHighestColumn() . '2'
    )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

    // Adiciona formula de sugestao
    for ($row = 3; $row <= $sheet->getHighestRow(); $row++) {
        $sheet->setCellValue("K" . $row, "=CEILING(MAX(I" . $row . ":J" . $row . ")*1.5,1)-SUM(G" . $row . ":H" . $row . ")");
    }

    // Comentário na coluna de vendas
    $objCommentRichText = $sheet->getComment('I2')->getText()->createTextRun('Média de venda dos últimos 3 meses.');
    $objCommentRichText->getFont()->setBold(true);
    $sheet->getComment('I2')->setWidth(200);
    $objCommentRichText = $sheet->getComment('J2')->getText()->createTextRun('Quantidade de vendas do mês atual.');
    $objCommentRichText->getFont()->setBold(true);
    $sheet->getComment('J2')->setWidth(200);


    // Comentário na coluna de sugestão
    $objCommentRichText = $sheet->getComment('K2')->getText()->createTextRun("Sugestão de pedido:\r\n");
    $objCommentRichText->getFont()->setBold(true);

    $sheet->getComment('K2')->getText()->createTextRun("Maior quantidade de vendas entre o mês atual e a média dos três últimos meses com o acréscimo de 50% de margem de segurança ");
    $sheet->getComment('K2')->getText()->createTextRun("menos somatório do estoque do escritório e da loja.");

    $sheet->getComment('K2')->getText()->createTextRun("\r\n\r\n");
    $sheet->getComment('K2')->getText()->createTextRun("Formula = Maximo(VENDA MEDIA 3 ULTIMOS MESES e VENDA MES ATUAL)+50% - Soma(ESTOQUE ESCRITORIO e ESTOQUE LOJA) ");

    $sheet->getComment('K2')->setHeight(200);
    $sheet->getComment('K2')->setWidth(300);

    // Cabeçalho
    $sheet->setCellValue('A1', 'Pedido');
    $sheet->setCellValue('G1', 'Estoque');
    $sheet->setCellValue('I1', 'Vendas');
    $sheet->setCellValue('K1', 'Compra');
    $sheet->mergeCells('A1:F1');
    $sheet->mergeCells('G1:H1');
    $sheet->mergeCells('I1:J1');
}

function adicionaCabecalho()
{
    return array(
        "Codigo",
        "Forn.",
        "Descricao",
        "Tam.",
        "Ref.",
        "Ped.",
        "Escr.",
        "Loja",
        "media 3 meses",
        "Mes atual",
        "Sugestao"
    );
}


$host = 'localhost:/Loja/REM_ago.FDB';
$username = 'sysdba';
$password = 'masterkey';

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

$produtos = array();
$produtos[] = adicionaCabecalho();

$fornecedorAnterior = "";
$fornecedorAtual = "";
$indWorksheet = 1;

// cria a planilha
$objPHPExcel = new PHPExcel();

// configura cache
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

// configura a linguagem
$locale = 'pt_br';
$validLocale = PHPExcel_Settings::setLocale($locale);
if (!$validLocale) {
    echo 'Unable to set locale to ' . $locale . " - reverting to en_us" . PHP_EOL;
}


while ($row = ibase_fetch_object($sth)) {

    $fornecedorAtual = $row->NOMEFOR;

    // Inicializa a variável e seta o título para o primeiro tipo de modelo
    if ($fornecedorAnterior == "") {
        $fornecedorAnterior = $fornecedorAtual;

        $objPHPExcel->getActiveSheet()->setTitle($fornecedorAnterior);
//        configuraDimensoesRelatorio($objPHPExcel);
    }

    // Se mudou o fornecedor então grava os dados e cria uma nova planilha
    if ($fornecedorAnterior != "" && (strcmp($fornecedorAnterior, $fornecedorAtual) != 0)) {

        $fornecedorAnterior = $fornecedorAtual;

        // Grava lista na planilha atual
        $objPHPExcel->getActiveSheet()
            ->fromArray(
                $produtos,
                NULL,
                'A2',
                true
            );

        // Limpa lista
        $produtos = [];
        $produtos[] = adicionaCabecalho();

        //Gera nova planilha
        $objWorkSheet = $objPHPExcel->createSheet($indWorksheet);
        $objWorkSheet->setTitle($fornecedorAtual);
        $objPHPExcel->setActiveSheetIndex($indWorksheet);
//        configuraDimensoesRelatorio($objPHPExcel);
        $indWorksheet++;

    }

    $escritorio = (int)trim($row->ESCRITORIO);
    if ($escritorio < 0)
        $escritorio = 0;

    $loja = (int)trim($row->LOJA);
    if ($loja < 0)
        $loja = 0;

    $produtos[] = array(
        trim($row->CODIGO),
        trim($row->NOMEFOR),
        trim($row->DESCRICAO),
        trim($row->TAMANHO),
        trim($row->REFERENCIA_FOR),
        "",
        $escritorio,
        $loja,
        trim($row->MEDIA_ULT_3_MESES),
        trim($row->MES_ATUAL)
    );

}

// Carrega da lista
$objPHPExcel->getActiveSheet()
    ->fromArray(
        $produtos,
        NULL,
        'A2',
        true
    );


for ($i = 0; $i < $indWorksheet; $i++) {
    $objPHPExcel->setActiveSheetIndex($i);
    configuraDimensoesRelatorio($objPHPExcel);
}

$objPHPExcel->setActiveSheetIndex(0);

ibase_free_result($sth);
ibase_close($dbh);


// salva o arquivo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
$objWriter->save($nomeRel);

echo "<br>Gravei o excel<br>";