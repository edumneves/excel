<?php
	include_once 'ItemFactory.php';
    include_once 'Camisa.php';
    include_once 'CamisaAgrupada.php';
    include_once 'ImportacaoGlobal.php';

	class Catalogo {
		private $listaCamisas;
        private $listaCamisasAgrupadas;
		private $listaAcessorios;

        function comparaCamisas(Camisa $item1, Camisa $item2){
            $cmpDescResumida = strcmp($item1->getDescricaoResumida(), $item2->getDescricaoResumida());
            if ($cmpDescResumida == 0){
                $cmpTipoModelo = strcmp($item1->getTipoModelo(), $item2->getTipoModelo());
                if ($cmpTipoModelo == 0){
                    global $tamanhos;
                    $ind1 = array_search($item1->getTamanho(), $tamanhos);
                    $ind2 = array_search($item2->getTamanho(), $tamanhos);
                    return ($ind1 - $ind2);
                }
                return $cmpTipoModelo;
            }
            return $cmpDescResumida;
        }
		
		public function montaCatalogo($sheetData){
			// Percorre os objetos montando os ites do estoque
			foreach($sheetData as $item) {
				$itemEstoque = ItemFactory::criaItemEstoque($item);
				if (is_object($itemEstoque)){
					$tipoItem = get_class($itemEstoque);
					switch ($tipoItem){
						case "Acessorio":
							$this->listaAcessorios[] = $itemEstoque;
							break;
						case "Camisa":
							$this->listaCamisas[] = $itemEstoque;
							break;
					}
				} 
			}

            //Ordena o array pela descricaoResumida, TipoModelo, Tamanho
            usort($this->listaCamisas, array($this, "comparaCamisas"));
            //Confere e cria os itens agrupados

            $camisaAgrupada = new CamisaAgrupada($this->listaCamisas[0]);
            for ($indCamisa = 1, $indCamisaAnterior = 0; $indCamisa < count($this->listaCamisas); $indCamisa++){
                $camisaAtual = $this->listaCamisas[$indCamisa];
                $camisaAnterior = $this->listaCamisas[$indCamisaAnterior];

                // Se as camisas são iguais
                if (strcmp($camisaAnterior->getTitulo(), $camisaAtual->getTitulo())==0){
                    // Se não tiver agrupamento, cria um novo e adiciona a camisa anterior
                    if (!isset($camisaAgrupada)){
                        $camisaAgrupada = new CamisaAgrupada($camisaAnterior);
                        $camisaAgrupada->addListaCamisa($camisaAnterior);
                    }

                    $camisaAgrupada->addListaCamisa($camisaAtual);

                } else {
                // Se as camisas são diferentes
                    // Se só tem uma camisa diferente, não precisa criar grupamento
                    if ($indCamisa - $indCamisaAnterior <= 1){
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


            $csvCamisas = fopen("camisasAgrupadas.csv", "w");
            //Confere e cria os itens agrupados
            foreach($this->listaCamisasAgrupadas as $camisa){
//                echo $camisa . "<br>";
                fwrite($csvCamisas, $camisa . "\n");
//                fputcsv($csvCamisas, $camisa, ";");
            }
            fclose($csvCamisas);

            $csvCamisas = fopen("camisas.csv", "w");
            //Confere e cria os itens agrupados
            foreach($this->listaCamisas as $camisa){
//                echo $camisa . "<br>";
                fwrite($csvCamisas, $camisa . "\n");
//                fputcsv($csvCamisas, $camisa, ";");
            }
            fclose($csvCamisas);


/*            echo "<br><br>";
            $retorno = "Tipo item ;";
            $retorno .= " Modelo ;";
            $retorno .= " Descricao Resumida ;";
            $retorno .= " Tamanho ;";
            $retorno .= " Cor ;";
            $retorno .= " Banda ;";
            $retorno .= " Descricao ;";
            $retorno .= " Cod Fornecedor ;";
            $retorno .= " Ref Fornecedor ;";
            $retorno .= " Preco Custo ;";
            $retorno .= " Preco Venda ;";
            $retorno .= " Saldo Estoque ;";

            echo $retorno . "<br>";

            foreach($this->listaCamisas as $camisa){
					echo $camisa . "<br>";
			}
*/
            $this->geraCatalogoCSV("C:\\xampp\\htdocs\\magento\\var\\import\\export.csv");
            $this->geraCatalogoDeleteCSV("C:\\xampp\\htdocs\\magento\\var\\import\\delete.csv");


            echo "<br/>Quantidade de Acessórios: " . count($this->listaAcessorios) . "<br>";
			echo "Quantidade de Camisas: " . count($this->listaCamisas) . "<br>";
            echo "Quantidade de Camisas agrupadas: " . count($this->listaCamisasAgrupadas) . "<br/>";
		}

        public function geraCatalogoDeleteCSV($caminho){
            $csv = fopen($caminho, "w");

            $header = array();
            $header[] = "sku";
            $header[] = "magmi:delete";
            fputcsv($csv, $header, ",");

            foreach($this->listaCamisas as $camisa){
                $listaApagar = array();
                $listaApagar[] = $camisa->getCodigo();
                $listaApagar[] = "1";
                fputcsv($csv, $listaApagar, ",");
            }

            foreach($this->listaCamisasAgrupadas as $camisa){
                $listaApagar = array();
                $listaApagar[] = $camisa->getCodigo();
                $listaApagar[] = "1";
                fputcsv($csv, $listaApagar, ",");
            }

            fclose($csv);

        }

        public function geraCatalogoCSV($caminho){

            $csv = fopen($caminho, "w");

            $header = $this->geraHeader();
            fputcsv($csv, $header, ",");

            foreach($this->listaCamisas as $camisa){
                fputcsv($csv, $this->geraCamisa($camisa), ",");
            }
            foreach($this->listaCamisasAgrupadas as $camisa){
                fputcsv($csv, $this->geraCamisa($camisa), ",");
            }
            fclose($csv);
        }

        private function geraCamisa ( $camisa){
            $textoCamisa = array();
            $textoCamisa[] = $camisa->getCodigo();  //sku
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
            $textoCamisa[] = "base";                // _product_websites
            $textoCamisa[] = $camisa->getBanda();   // banda

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
            $textoCamisa[] = $camisa->getRefFornecedor(); // has_options
            //media_gallery
            //meta_description
            //meta_keyword
            //meta_title
            //minimal_price
            $textoCamisa[] = $camisa->getTipoModeloExtenso(); // modelo
            //msrp
            // msrp_display_actual_price_type
            if ($camisa->isSimpleProduct()){
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
            if ($camisa->isSimpleProduct()){
                if (!$camisa->getTemGrupo())
                    $textoCamisa[] = "4";
                else
                    $textoCamisa[] = "1";
            } else {
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
            if($camisa->getSaldo()>0 || !$camisa->isSimpleProduct())
                $textoCamisa[] = "1";
            else
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
            if ($camisa->feminina())
                $textoCamisa[] = "Camisas femininas::1::1::1/Baby Look::1::1::1";
            else
                 $textoCamisa[] = "Camisas masculinas::1::1::1";


            //configurable_attributes
            if ($camisa->isSimpleProduct()){
                $textoCamisa[] = "";
            } else{
                $textoCamisa[] = "tamanho";
            }
            //simples_skus
            if ($camisa->isSimpleProduct()){
                $textoCamisa[] = "";
            } else{
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


    }

?>