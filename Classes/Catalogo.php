<?php
	include_once 'ItemFactory.php';
	
	class Catalogo {
		private $listaCamisas;
		private $listaAcessorios;
		
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
/*
			
			foreach($listaCamisas as $camisa){
				if ($camisa->getBanda() == ""){
					echo $camisa . "<br>";
				}
			}
*/			
			echo "<br/>Quantidade de Acessórios: " . count($listaAcessorios) . "<br>";
			echo "Quantidade de Camisas: " . count($listaCamisas) . "<br>";
		}
		
		
}

?>