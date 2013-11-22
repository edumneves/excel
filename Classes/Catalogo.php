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
							$listaAcessorios[] = $itemEstoque;
							break;
						case "Camisa":
							$listaCamisas[] = $itemEstoque;
							break;
					}
				} 
			}

            //Ordena o array pela descricaoResumida, TipoModelo, Tamanho
            usort($listaCamisas, array($this, "comparaCamisas"));
            //Confere e cria os itens agrupados

            $camisaAgrupada = new CamisaAgrupada($listaCamisas[10]);
            echo "<br><br>" . $camisaAgrupada . "<br>" . $listaCamisas[10] . "<br><br>";

//
//            for ($indCamisa = 1, $camisaAnterior = $listaCamisas[0]; $indCamisa < count($listaCamisas); $indCamisa++){
//                $camisaAtual = $listaCamisas[$indCamisa];
//
//                // Se não tem camisa anterior
//                if ($camisaAnterior )
//
//
//                // Já tem camisa anterior
//                if ((strcmp($camisaAgrupada->getDescricaoResumida(), $camisaAtual->getDescricaoResumida())==0) &&
//                    (strcmp($camisaAgrupada->getTipoModelo(),$camisaAtual->getTipoModelo())==0){
//                    $camisaAgrupada->addListaCamisa($camisaAtual);
//
//                }
//            }

            $csvCamisas = fopen("camisas.csv", "w");
            //Confere e cria os itens agrupados
            foreach($listaCamisas as $camisa){
                echo $camisa . "<br>";
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

            foreach($listaCamisas as $camisa){
					echo $camisa . "<br>";
			}

            $this->geraCatalogoCSV("export.csv");*/

			echo "<br/>Quantidade de Acessórios: " . count($listaAcessorios) . "<br>";
			echo "Quantidade de Camisas: " . count($listaCamisas) . "<br>";
		}

        public function geraCatalogoCSV($caminho){
            $csv = fopen($caminho, "w");

            $header = array();
            $header[] = "sku";
            $header[] = "_attribute_set";
            $header[] = "_type";
            $header[] = "_category";
            $header[] = "_root_category";
            $header[] = "_product_websites";
            $header[] = "banda";
            $header[] = "color";
            $header[] = "country_of_manufacture";
            $header[] = "created_at";
            $header[] = "description";
            $header[] = "enable_googlecheckout";
            $header[] = "has_options";
            $header[] = "image";
            $header[] = "image_label";
            $header[] = "manufacturer";
            $header[] = "modelo";
            $header[] = "msrp_display_actual_price_type";
            $header[] = "msrp_enabled";
            $header[] = "name";
            $header[] = "options_container";
            $header[] = "price";
            $header[] = "reffornecedor";
            $header[] = "required_options";
            $header[] = "short_description";
            $header[] = "small_image";
            $header[] = "status";
            $header[] = "tamanho";
            $header[] = "tax_class_id";
            $header[] = "thumbnail";
            $header[] = "updated_at";
            $header[] = "url_key";
            $header[] = "url_path";
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
            $header[] = "use_config_notify_stock_qty";
            $header[] = "manage_stock";
            $header[] = "use_config_manage_stock";
            $header[] = "stock_status_changed_auto";
            $header[] = "use_config_qty_increments";
            $header[] = "qty_increments";
            $header[] = "use_config_enable_qty_inc";
            $header[] = "enable_qty_increments";
            $header[] = "is_decimal_divided";
            $header[] = "_media_attribute_id";
            $header[] = "_media_image";
            $header[] = "_media_position";
            $header[] = "_media_is_disabled";

            fputcsv($csv, $header, ",");

            fclose($csv);
        }
		
		
}

?>