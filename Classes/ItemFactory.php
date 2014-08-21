<?php
	include_once 'ImportacaoGlobal.php';
	include_once 'Camisa.php';
	include_once 'Acessorio.php';
	
	class ItemFactory {
		public static function criaItemEstoque($item){

			$tipoItem = ImportacaoGlobal::getTipoItem($item);
			switch ($tipoItem) {
				case "Acessorio":
					if (Acessorio::valido($item)) {
						return (new Acessorio($item));
					}
					error_log("Acessorio inválido. " . ImportacaoGlobal::extraiCodigo($item) );
                    return ;
				case "Camisa":
					if (Camisa::valido($item)){
						return (new Camisa($item));
					}
                    echo "Camisa inválida. " . ImportacaoGlobal::extraiCodigo($item) . "<br>";
					error_log("Camisa inválida. " . ImportacaoGlobal::extraiCodigo($item));
					return;
				default: 
					error_log("Tipo de item não encontrado. Tipo = " . $tipoItem);
					return;
			}
		}
	}
?>