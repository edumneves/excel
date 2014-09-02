<?php
include_once 'ItemFactory.php';
include_once 'Camisa.php';
include_once 'CamisaAgrupada.php';
include_once 'ImportacaoGlobal.php';
include_once 'Conf.php';

class Catalogo
{
    private $listaCamisas;
    private $listaCamisasAgrupadas;
    private $listaAcessorios;
    private $listaCamisasHabilitadas;
    private $listaRelatorio;
    private $listaBD;

    // Estoque mínimo para mostrar em relatório
    function getQuantMinEstoque($tipoModelo)
    {
        global $rel_quant_min;

        if (array_key_exists($tipoModelo, $rel_quant_min)) {
            return $rel_quant_min[$tipoModelo];
        }
        error_log("Tipo de modelo (" . $tipoModelo . ") sem quantidade mínima definido. Usando 0.");
        return 0;
    }

    // Compara por TipoModelo, Descrição Resumida, Tamanho
    function comparaCamisas(Camisa $item1, Camisa $item2)
    {
        $cmpTipoModelo = strcmp($item1->getTipoModelo(), $item2->getTipoModelo());
        if ($cmpTipoModelo == 0) {
            $cmpDescResumida = strcmp($item1->getDescricaoResumida(), $item2->getDescricaoResumida());
            if ($cmpDescResumida == 0) {
                global $tamanhos;
                $ind1 = array_search($item1->getTamanho(), $tamanhos);
                $ind2 = array_search($item2->getTamanho(), $tamanhos);
                return ($ind1 - $ind2);
            }
            return $cmpDescResumida;
        }
        return $cmpTipoModelo;
    }

    // Compara por TipoModelo, Fornecedor, Descrição Resumida, Tamanho
    function comparaCamisasRelatorio(Camisa $item1, Camisa $item2)
    {
        $cmpTipoModelo = strcmp($item1->getTipoModelo(), $item2->getTipoModelo());
        if ($cmpTipoModelo == 0) {

            $cmpFornecedor = strcmp($item1->getCodFornecedor(), $item2->getCodFornecedor());
            if ($cmpFornecedor == 0) {
                $cmpDescResumida = strcmp($item1->getDescricaoResumida(), $item2->getDescricaoResumida());
                if ($cmpDescResumida == 0) {
                    // Define ordem dos tamanhos de acordo com array global
                    global $tamanhos;
                    $ind1 = array_search($item1->getTamanho(), $tamanhos);
                    $ind2 = array_search($item2->getTamanho(), $tamanhos);
                    return ($ind1 - $ind2);
                }
                return $cmpDescResumida;
            }
            return $cmpFornecedor;
        }
        return $cmpTipoModelo;
    }

    public function montaCatalogo($sheetData, $listaBDext)
    {
        $this->listaBD = $listaBDext;

        // Percorre os objetos montando os ites do estoque
        foreach ($sheetData as $item) {
            $itemEstoque = ItemFactory::criaItemEstoque($item);
            if (is_object($itemEstoque)) {
                $tipoItem = get_class($itemEstoque);
                switch ($tipoItem) {
                    case "Acessorio":
                        $this->listaAcessorios[] = $itemEstoque;
                        break;
                    case "Camisa":
                        $this->listaCamisas[] = $itemEstoque;
                        break;
                }
            }
        }

        //Ordena o array pelo TipoModelo, descricaoResumida, Tamanho
        usort($this->listaCamisas, array($this, "comparaCamisas"));
        //Confere e cria os itens agrupados

        $camisaAgrupada = new CamisaAgrupada($this->listaCamisas[0]);
        for ($indCamisa = 1, $indCamisaAnterior = 0; $indCamisa < count($this->listaCamisas); $indCamisa++) {
            $camisaAtual = $this->listaCamisas[$indCamisa];
            $camisaAnterior = $this->listaCamisas[$indCamisaAnterior];

            // Se as camisas são iguais
            if (strcmp($camisaAnterior->getTitulo(), $camisaAtual->getTitulo()) == 0) {
                // Se não tiver agrupamento, cria um novo e adiciona a camisa anterior
                if (!isset($camisaAgrupada)) {
                    $camisaAgrupada = new CamisaAgrupada($camisaAnterior);
                    $camisaAgrupada->addListaCamisa($camisaAnterior);
                }

                $camisaAgrupada->addListaCamisa($camisaAtual);

            } else {
                // Se as camisas são diferentes
                // Se só tem uma camisa diferente, não precisa criar grupamento
                if ($indCamisa - $indCamisaAnterior <= 1) {
                    if (isset($camisaAgrupada))
                        unset ($camisaAgrupada);
                } else {
                    // Se tem mais de uma camisa diferente, tem que
                    $this->listaCamisasAgrupadas[] = $camisaAgrupada;
                    unset ($camisaAgrupada);
                }
                $indCamisaAnterior = $indCamisa;
            }
        }


        $this->geraCamisasAgrupadas("./Saida_site/camisasAgrupadas.csv");

        $csvCamisas = fopen("./Saida_site/camisas.csv", "w");
        //Confere e cria os itens agrupados
        foreach ($this->listaCamisas as $camisa) {
            fwrite($csvCamisas, $camisa . "\n");
        }
        fclose($csvCamisas);
        echo "camisas.csv - gerado<br/>";

        $this->geraCatalogoCSV("./Saida_site/export.csv");
        echo "export.csv - gerado<br/>";

        /*
        // Gera lista de camisas habilitadas na loja, usada como insumo para montar imagens
        usort($this->listaCamisasHabilitadas, array($this, "comparaCamisas"));
        $csvCamisasHabilitadas = fopen("./Saida_site/camisasHabilitadas.csv", "w");
        $csvBabyLooksHabilitadas = fopen("./Saida_site/babysHabilitadas.csv", "w");
        //Confere e cria os itens agrupados
        foreach($this->listaCamisasHabilitadas as $camisa){
            if ($camisa->getTipoModelo() == 'CM')
                fwrite($csvCamisasHabilitadas, $camisa->getCodigo() . ";" . $camisa->getTitulo() . "\n");
            else
                fwrite($csvBabyLooksHabilitadas, $camisa->getCodigo() . ";" . $camisa->getTitulo() . "\n");
        }
        fclose($csvCamisasHabilitadas);
        fclose($csvBabyLooksHabilitadas);
        echo "CamisasHabilitadas.csv - gerado<br/>";
        echo "BabyLooksHabilitadas.csv - gerado<br/>";
        */

        // Gera lista de camisas habilitadas na loja, usada como insumo para montar imagens
        usort($this->listaCamisasHabilitadas, array($this, "comparaCamisas"));
        $csvCamisasHabilitadas = fopen("./Saida_site/camisasHabilitadas.csv", "w");
        $csvCamisasFaltando = fopen("./Saida_site/camisasFaltandoFoto.csv", "w");
        //Confere e cria os itens agrupados
        fwrite($csvCamisasHabilitadas, "sku;titulo;image;media_gallery;small_image;thumbnail;status\n");


        fwrite($csvCamisasFaltando, "sku;titulo;codBarra;CodFornecedor;TipoModelo;\n");
        foreach ($this->listaCamisasHabilitadas as $camisa) {
            $temImagem = false;
            $temImagemCostas = false;
            //$caminhoFotos = "/Fotos/Loja/Ultimas_fotos/fotos_site_com_marca/";

            $caminhoImport = "/Applications/MAMP/htdocs/jfx/media/import/";
            $caminhoFotos = "fotos_site_com_marca/";
            $fileName = $caminhoFotos . "0" . $camisa->getCodImagem() . ".jpg";
            $fileNameCostas = $caminhoFotos . "0" . $camisa->getCodImagem() . "_C.jpg";
            $temImagem = file_exists($caminhoImport . $fileName);
            $temImagemCostas = file_exists($caminhoImport . $fileNameCostas);

            $mediaGallery = $fileName;
            $small = $caminhoFotos . "small/" . "0" . $camisa->getCodImagem() . ".jpg";
            $thumb = $caminhoFotos . "thumb/" . "0" . $camisa->getCodImagem() . ".jpg";
            $mediaGallery = $fileName;

            //fwrite($csvCamisasHabilitadas,$camisa->getCodigo() . ";" . $camisa->getTitulo() . ";0" . $camisa->getCodImagem() . ";" . $temImagem. ";" . $temImagemCostas . ";" . "\n");
            if ($temImagem) {
                if ($temImagemCostas) {
                    $mediaGallery = "\"" . $mediaGallery . ";" . $fileNameCostas . "\"";
                }
                fwrite($csvCamisasHabilitadas, $camisa->getCodigo() . ";" . $camisa->getTitulo() . ";+" . $fileName . ";" . $mediaGallery . ";" . $small . ";" . $thumb . ";" . "1" . "\n");
            } else {
                // Desabilita produtos sem imagem
                fwrite($csvCamisasHabilitadas, $camisa->getCodigo() . ";" . $camisa->getTitulo() . ";" . "" . ";" . "" . ";" . "" . ";" . "" . ";" . "2" . "\n");

                // Grava no arquivo indicando que não tem foto
                fwrite($csvCamisasFaltando, $camisa->getCodigo() . ";" . $camisa->getTitulo() . ";" . "0" . $camisa->getCodImagem() . ";" . $camisa->getCodFornecedor() . ";" . $camisa->getTipoModelo() . "\n");

            }
        }
        fclose($csvCamisasHabilitadas);
        fclose($csvCamisasFaltando);
        echo "CamisasHabilitadas.csv - gerado<br/>";

        // Atualização de preços dos itens
        $csvCamisasHabilitadas = fopen("./Saida_site/camisasPrecos.csv", "w");
        fwrite($csvCamisasHabilitadas, "sku;price\n");
        foreach ($this->listaCamisasHabilitadas as $camisa) {
            $preco = 0;
            switch ($camisa->getTipoModelo()) {
                case "BY":
                case "IN":
                    $preco = 25;
                    break;
                case "CM":
                case "BL":
                case "MC":
                    $preco = 30;
                    break;
            }
            fwrite($csvCamisasHabilitadas, $camisa->getCodigo() . ";" . $preco . "\n");
        }
        fclose($csvCamisasHabilitadas);


        $this->geraCatalogoDeleteCSV("./Saida_site/delete.csv");
        echo "delete.csv - gerado<br/><br/>";

        // Gera relatórios diários para repor estoques
        $this->geraRelatorioEstoque("./Saida/Relatorio_Estoque.xlsx", 0, 0);
        $this->geraRelatorioEstoque("./Saida/Relatorio_Estoque_Resumido.xlsx", 1, 0);
        $this->geraRelatorioEstoque("./Saida/Relatorio_Estoque_Resumido_Escritorio.xlsx", 1, 1);
        $this->geraRelatorioBones();


        echo "<br/>Quantidade de Acessórios: " . count($this->listaAcessorios) . "<br>";
        echo "Quantidade de Camisas: " . count($this->listaCamisas) . "<br>";
        echo "Quantidade de Camisas agrupadas: " . count($this->listaCamisasAgrupadas) . "<br/>";
    }

    public function geraCatalogoDeleteCSV($caminho)
    {
        $csv = fopen($caminho, "w");

        $header = array();
        $header[] = "sku";
        $header[] = "magmi:delete";
        fputcsv($csv, $header, ",");

        foreach ($this->listaCamisas as $camisa) {
            $listaApagar = array();
            $listaApagar[] = $camisa->getCodigo();
            $listaApagar[] = "1";
            fputcsv($csv, $listaApagar, ",");
        }

        foreach ($this->listaCamisasAgrupadas as $camisa) {
            $listaApagar = array();
            $listaApagar[] = $camisa->getCodigo();
            $listaApagar[] = "1";
            fputcsv($csv, $listaApagar, ",");
        }

        fclose($csv);

    }

    public function geraCatalogoCSV($caminho)
    {

        $csv = fopen($caminho, "w");

        $header = $this->geraHeader();
        fputcsv($csv, $header, ",");

        foreach ($this->listaCamisas as $camisa) {
            fputcsv($csv, $this->geraCamisa($camisa), ",");
        }
        foreach ($this->listaCamisasAgrupadas as $camisa) {
            fputcsv($csv, $this->geraCamisa($camisa), ",");
        }
        fclose($csv);
    }

    private function geraCamisa($camisa)
    {
        $textoCamisa = array();
        $textoCamisa[] = $camisa->getCodigo(); //sku
        //$header[] = "_store";
        $textoCamisa[] = "Camisas"; // _attribute_set

        // _type
        if ($camisa->isSimpleProduct())
            $textoCamisa[] = "simple";
        else
            $textoCamisa[] = "configurable";

        // _category
        /*            if ($camisa->feminina())
                        $textoCamisa[] = "Camisas femininas";
                    else
                        $textoCamisa[] = "Camisas masculinas";
                    $textoCamisa[] = "Default Category";    //_root_category*/
        $textoCamisa[] = "base"; // _product_websites
        $textoCamisa[] = $camisa->getBanda(); // banda

        // color
        if ($camisa->getCor() == "")
            $textoCamisa[] = $camisa->getCor();
        else
            $textoCamisa[] = "Preta";
        //$header[] = "cost";
        $textoCamisa[] = "Brasil"; // country_of_manufacture
        //$header[] = "created_at";
        // custom_design
        // custom_design_from
        // custom_design_to
        // custom_layout_update
        $textoCamisa[] = $camisa->getTitulo(); // description
        $textoCamisa[] = "0"; // enable_googlecheckout
        //gallery
        //gift_message_available
        $textoCamisa[] = "0"; // has_options
        //$header[] = "image";
        //$header[] = "image_label";
        $textoCamisa[] = $camisa->getCodFornecedor(); // manufacturer
        //media_gallery
        //meta_description
        //meta_keyword
        //meta_title
        //minimal_price
        $textoCamisa[] = $camisa->getTipoModeloExtenso(); // modelo
        //msrp
        // msrp_display_actual_price_type
        if ($camisa->isSimpleProduct()) {
            if ($camisa->getTemGrupo())
                $textoCamisa[] = "No Carrinho";
            else
                $textoCamisa[] = "Utilizar configuração";
        } else {
            $textoCamisa[] = "Utilizar configuração";
        }

        $textoCamisa[] = "Utilizar configuração"; // msrp_enabled
        $textoCamisa[] = $camisa->getTitulo(); // name
        //news_from_date
        //news_to_date
        $textoCamisa[] = "Bloco depois da Coluna de Informação"; //options_container

        // price
        if ($camisa->getTipoModelo() == "BL")
            $textoCamisa[] = "20";
        else
            $textoCamisa[] = "25";

        $textoCamisa[] = $camisa->getRefFornecedor(); //reffornecedor
        $textoCamisa[] = "0"; //required_options
        $textoCamisa[] = $camisa->getTitulo(); //short_description
        //small_image
        //small_image_label
        //special_from_date
        //special_price
        //special_to_dat
        $textoCamisa[] = "1"; //status
        $textoCamisa[] = $camisa->getTamanho(); //tamanho
        $textoCamisa[] = "0"; //tax_class_id
        //thumbnail
        //thumbnail_label
        //$header[] = "updated_at";
        //$header[] = "url_key";
        //$header[] = "url_path";

        //visibility
        if ($camisa->isSimpleProduct()) {
            if (!$camisa->getTemGrupo()) {
                $textoCamisa[] = "4";
                $this->listaCamisasHabilitadas[] = $camisa;
            } else
                $textoCamisa[] = "1";
        } else {
            $this->listaCamisasHabilitadas[] = $camisa;
            $textoCamisa[] = "4";
        }

        $textoCamisa[] = "250"; //weight
        $textoCamisa[] = $camisa->getSaldo(); //qty
        $textoCamisa[] = "0"; //min_qty
        $textoCamisa[] = "1"; //use_config_min_qty
        $textoCamisa[] = "0"; //is_qty_decimal
        $textoCamisa[] = "0"; //backorders
        $textoCamisa[] = "1"; //use_config_backorders
        $textoCamisa[] = "1"; //min_sale_qty
        $textoCamisa[] = "1"; //use_config_min_sale_qty
        $textoCamisa[] = "0"; //max_sale_qty
        $textoCamisa[] = "1"; //use_config_max_sale_qty

        //is_in_stock
        if ($camisa->getSaldo() > 0 || !$camisa->isSimpleProduct()) {
            $textoCamisa[] = "1";
        } else
            $textoCamisa[] = "0";

        //notify_stock_qty
        $textoCamisa[] = "1"; //use_config_notify_stock_qty
        $textoCamisa[] = "0"; //manage_stock
        $textoCamisa[] = "1"; //use_config_manage_stock
        $textoCamisa[] = "0"; //stock_status_changed_auto
        $textoCamisa[] = "1"; //use_config_qty_increments
        $textoCamisa[] = "0"; //qty_increments
        $textoCamisa[] = "1"; //use_config_enable_qty_inc
        $textoCamisa[] = "0"; //enable_qty_increments
        $textoCamisa[] = "0"; //is_decimal_divided
        //_links_related_sku
        //_links_related_position
        //_links_crosssell_sku
        //_links_crosssell_position
        //_links_upsell_sku
        //_links_upsell_position
        //_associated_sku
        //_associated_default_qty
        //_associated_position
        //_tier_price_website
        //_tier_price_customer_group
        //_tier_price_qty
        //_tier_price_price
        //_group_price_website
        //_group_price_customer_group
        //_group_price_price
        //$header[] = "_media_attribute_id";
        //$header[] = "_media_image";
        $textoCamisa[] = "1"; //_media_position
        $textoCamisa[] = "0"; //_media_is_disabled
        //categories

        switch ($camisa->getTipoModelo()) {
            case "BL":
                $textoCamisa[] = "Camisas femininas::1::1::1/Baby Look::1::1::1";
                break;
            case "CM":
                if ($camisa->getCategoria() == "") {
                    $textoCamisa[] = "Camisas masculinas::1::1::1/Outros::1::1::1";
                } else
                    $textoCamisa[] = "Camisas masculinas::1::1::1/" . $camisa->getCategoria() . "::1::1::1";
                break;
            case "BY":
                $textoCamisa[] = "Artigos infantis::1::1::1/Body::1::1::1";
                break;
            case "IN":
                $textoCamisa[] = "Artigos infantis::1::1::1/Camisas infantis::1::1::1";
                break;
            case "MC":
                $textoCamisa[] = "Camisas femininas::1::1::1/Gola gaída::1::1::1";
                break;
        }


        //configurable_attributes
        if ($camisa->isSimpleProduct()) {
            $textoCamisa[] = "";
        } else {
            $textoCamisa[] = "tamanho";
        }
        //simples_skus
        if ($camisa->isSimpleProduct()) {
            $textoCamisa[] = "";
        } else {
            $textoCamisa[] = $camisa->getListaSkus();
        }

        return $textoCamisa;
    }

    /**
     * @return array
     */
    private function geraHeader()
    {
        $header = array();
        $header[] = "sku";
        //$header[] = "store";
        $header[] = "attribute_set";
        $header[] = "type";
//            $header[] = "category";
//            $header[] = "root_category";
        $header[] = "product_websites";
        $header[] = "banda";
        $header[] = "color";
        //$header[] = "cost";
        $header[] = "country_of_manufacture";
        //$header[] = "created_at";
        // custom_design
        // custom_design_from
        // custom_design_to
        // custom_layout_update
        $header[] = "description";
        $header[] = "enable_googlecheckout";
        //gallery
        //gift_message_available
        $header[] = "has_options";
        //$header[] = "image";
        //$header[] = "image_label";
        $header[] = "manufacturer";
        //media_gallery
        //meta_description
        //meta_keyword
        //meta_title
        //minimal_price
        $header[] = "modelo";
        //msrp
        $header[] = "msrp_display_actual_price_type";
        $header[] = "msrp_enabled";
        $header[] = "name";
        //news_from_date
        //news_to_date
        $header[] = "options_container";
        //page_layout
        $header[] = "price";
        $header[] = "reffornecedor";
        $header[] = "required_options";
        $header[] = "short_description";
        //small_image
        //small_image_label
        //special_from_date
        //special_price
        //special_to_date
        $header[] = "status";
        $header[] = "tamanho";
        $header[] = "tax_class_id";
        //thumbnail
        //thumbnail_label
        //$header[] = "updated_at";
        //$header[] = "url_key";
        //$header[] = "url_path";
        $header[] = "visibility";
        $header[] = "weight";
        $header[] = "qty";
        $header[] = "min_qty";
        $header[] = "use_config_min_qty";
        $header[] = "is_qty_decimal";
        $header[] = "backorders";
        $header[] = "use_config_backorders";
        $header[] = "min_sale_qty";
        $header[] = "use_config_min_sale_qty";
        $header[] = "max_sale_qty";
        $header[] = "use_config_max_sale_qty";
        $header[] = "is_in_stock";
        //notify_stock_qty
        $header[] = "use_config_notify_stock_qty";
        $header[] = "manage_stock";
        $header[] = "use_config_manage_stock";
        $header[] = "stock_status_changed_auto";
        $header[] = "use_config_qty_increments";
        $header[] = "qty_increments";
        $header[] = "use_config_enable_qty_inc";
        $header[] = "enable_qty_increments";
        $header[] = "is_decimal_divided";
        //_links_related_sku
        //_links_related_position
        //_links_crosssell_sku
        //_links_crosssell_position
        //_links_upsell_sku
        //_links_upsell_position
        //_associated_sku
        //_associated_default_qty
        //_associated_position
        //_tier_price_website
        //_tier_price_customer_group
        //_tier_price_qty
        //_tier_price_price
        //_group_price_website
        //_group_price_customer_group
        //_group_price_price
        //$header[] = "_media_attribute_id";
        //$header[] = "_media_image";
        $header[] = "media_position";
        $header[] = "media_is_disabled";
        $header[] = "categories";
        $header[] = "configurable_attributes";
        $header[] = "simples_skus";

        return $header;
    }

    /**
     * @return array
     */
    private function geraCamisasAgrupadas($caminho)
    {
        $csvCamisas = fopen($caminho, "w");
        //Confere e cria os itens agrupados
        foreach ($this->listaCamisasAgrupadas as $camisa) {
            fwrite($csvCamisas, $camisa . "\n");
        }
        fclose($csvCamisas);
        echo "camisasAgrupadas.csv - gerado<br/>";
    }

    private function configuraExcel($objPHPExcel)
    {

        // configura cache
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

        // configura a linguagem
        $locale = 'pt_br';
        $validLocale = PHPExcel_Settings::setLocale($locale);
        if (!$validLocale) {
            echo 'Unable to set locale to ' . $locale . " - reverting to en_us" . PHP_EOL;
        }
    }

    private function geraRelatorioEstoque($nomeRel, $filtra, $filtra_escritorio)
    {

        // cria a planilha
        $objPHPExcel = new PHPExcel();

        $this->configuraExcel($objPHPExcel);

        $this->montaRelatorio($objPHPExcel, $filtra, $filtra_escritorio);


        // salva o arquivo
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save($nomeRel);

        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);

        echo "<br>Gravei o excel<br>";
    }

    private function adicionaCabecalho()
    {
        return
            array(
                "DESCRICAO",
                "TAMANHO", // Tamanho
                "SALDO", // Saldo
                "CÓDIGO BARRAS", // Código de Barras
                "", // Vazio para preencher as movimentações
                "CÓDIGO FORNECEDOR", // Código Fornecedor
                "TIPO MODELO" // Tipo Modelo
            );

    }

    private function montaRelatorio(PHPExcel $objPHPExcel, $filtra, $filtra_escritorio)
    {
        $listaRelatorio = [];

        $listaItensRelatorio = $this->listaCamisas;

        //Ordena o array pelo TipoModelo, descricaoResumida, Tamanho
        usort($listaItensRelatorio, array($this, "comparaCamisasRelatorio"));

        $listaRelatorio[] = $this->adicionaCabecalho();
        //Monta Array que vai ser o destino do relatorio

        $tipoModeloAnterior = "";
        $indWorksheet = 1;
        echo "Geração de planilha<br>";
//            var_dump($this->listaBD);
        echo "<br><br>";
        foreach ($listaItensRelatorio as $itemRelatorio) {
            $tipoModeloAtual = $itemRelatorio->getTipoModelo();

            // Inicializa a variável e seta o título para o primeiro tipo de modelo
            if ($tipoModeloAnterior == "") {
                $tipoModeloAnterior = $tipoModeloAtual;

                $objPHPExcel->getActiveSheet()->setTitle($itemRelatorio->getTipoModeloExtenso());
                $this->configuraDimensoesRelatorio($objPHPExcel);
            }

            // Se mudou o TipoModelo então grava os dados e cria uma nova planilha
            if ($tipoModeloAnterior != "" && (strcmp($tipoModeloAnterior, $tipoModeloAtual) != 0)) {

                $tipoModeloAnterior = $tipoModeloAtual;

                // Grava lista na planilha atual
                $objPHPExcel->getActiveSheet()
                    ->fromArray(
                        $listaRelatorio,
                        NULL,
                        'A1'
                    );
                $this->configuraDimensoesRelatorio($objPHPExcel);

                // Limpa lista
                $listaRelatorio = [];
                $listaRelatorio[] = $this->adicionaCabecalho();

                //Gera nova planilha
                $objWorkSheet = $objPHPExcel->createSheet($indWorksheet);
                $objWorkSheet->setTitle($itemRelatorio->getTipoModeloExtenso());
                $objPHPExcel->setActiveSheetIndex($indWorksheet);
                $this->configuraDimensoesRelatorio($objPHPExcel);
                $indWorksheet++;

                echo "Nova aba " . $itemRelatorio->getTipoModelo() . " --> " . $itemRelatorio->getTipoModeloExtenso() . "<br>\n\r";
            }

            $codBarraAtual = "0" . $itemRelatorio->getCodigoBarra();
            $quantEstoqueMinimo = $this->getQuantMinEstoque($tipoModeloAtual);
            $quantAtual = (int)$itemRelatorio->getSaldo();
            if ($filtra && array_key_exists($codBarraAtual, $this->listaBD)) {
                if ($quantAtual < $quantEstoqueMinimo) {
                    if (!$filtra_escritorio || $this->listaBD[$codBarraAtual]['ESC'] > 0) {
                        // Adiciona a camisa
                        $listaRelatorio[] = array(
                            $itemRelatorio->getDescricaoResumida(),
                            $itemRelatorio->getTamanho(),
                            $itemRelatorio->getSaldo(),
                            $itemRelatorio->getCodigoBarra(),
                            "",
                            $itemRelatorio->getCodFornecedor(),
                            $itemRelatorio->getTipoModelo()
                        );
                    }
                }
            } else {
                if (!$filtra) {
                    // Se não encontrou no escritório Adiciona a camisa
                    $listaRelatorio[] = array(
                        $itemRelatorio->getDescricaoResumida(),
                        $itemRelatorio->getTamanho(),
                        $itemRelatorio->getSaldo(),
                        $itemRelatorio->getCodigoBarra(),
                        "",
                        $itemRelatorio->getCodFornecedor(),
                        $itemRelatorio->getTipoModelo()
                    );
                }
            }

        }

        // Carrega da lista
        $objPHPExcel->getActiveSheet()
            ->fromArray(
                $listaRelatorio,
                NULL,
                'A1'
            );

        $this->configuraDimensoesRelatorio($objPHPExcel);
        $objPHPExcel->setActiveSheetIndex(0);
    }

    private function configuraDimensoesRelatorio($objPHPExcel)
    {
        // Ajuste de tamanho das colunas
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(2);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(6);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(4);

        // Coluna de input
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->getStyle("E2:E" . $sheet->getHighestRow())->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'e9e1a3')
                )
            )
        );


        // Colunas de Sugestão e Pedido em negrito
        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );
        $sheet->getStyle("E2:E" . $sheet->getHighestRow())->applyFromArray($styleArray);

        /** Borders for all data */
        $objPHPExcel->getActiveSheet()->getStyle(
            'A2:' .
            $objPHPExcel->getActiveSheet()->getHighestColumn() .
            $objPHPExcel->getActiveSheet()->getHighestRow()
        )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


        /** Borders for heading */
        $objPHPExcel->getActiveSheet()->getStyle(
            'A1:' .
            $objPHPExcel->getActiveSheet()->getHighestColumn() . '1'
        )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);


    }

    private function geraRelatorioBones()
    {
        try {
            $objPHPExcelBone = PHPExcel_IOFactory::load("./Entrada/bones.xls");
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo("./Entrada/bones.xls", PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $sheetData = $objPHPExcelBone->getActiveSheet()->toArray(null, true, true, true);

        // Apaga a primeira linha
        unset($sheetData[1]);

        $listaBone = [];

        foreach ($sheetData as $bone) {
            $codigo = $bone["A"];
            $tamCodigo = strlen($codigo);
            $tipoModelo = substr($codigo, 2, 2);
            $tamanho = substr($codigo, 4, 1);


            // Adiciona a camisa
            $listaBone[] = array(
                trim(str_replace("ACESSORIOS BONES ", "", $bone["C"])), // Descricao Resumida
                trim($tamanho), // Tamanho
                trim($bone["H"]), // Saldo
                trim($bone["B"]), // Codigo Barra
                "", // Vai preencher com a quantidade atualizada
                trim($bone["D"]), //  $itemRelatorio->getCodFornecedor(),
                trim($tipoModelo) //  $itemRelatorio->getTipoModelo()
            );
        }

        // ordena descrições
        usort($listaBone, array($this, "comparaBones"));

        // cria a planilha
        $objPHPExcel = new PHPExcel();

        $this->configuraExcel($objPHPExcel);

        // Carrega da lista
        $objPHPExcel->getActiveSheet()
            ->fromArray(
                $listaBone,
                NULL,
                'A2'
            );

        // Carrega da lista
        $objPHPExcel->getActiveSheet()
            ->fromArray(
                $this->adicionaCabecalho(),
                NULL,
                'A1'
            );

        $this->configuraDimensoesRelatorio($objPHPExcel);

        $objPHPExcel->getActiveSheet()->setTitle("Bones");

        // salva o arquivo
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save("./Saida/Relatorio_Estoque_Bones.xlsx");

        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);

        echo "<br>Gravei o excel<br>";

    }

    // Compara por TipoModelo, Descrição Resumida, Tamanho
    function comparaBones($item1, $item2)
    {
        $cmpDescResumida = strcmp($item1[0], $item2[0]);
        if ($cmpDescResumida == 0) {
            global $tamanhos;
            $ind1 = array_search($item1[0], $tamanhos);
            $ind2 = array_search($item2[0], $tamanhos);
            return ($ind1 - $ind2);
        }
        return $cmpDescResumida;
    }

}

?>