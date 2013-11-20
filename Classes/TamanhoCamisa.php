<?php
	class TamanhoCamisa {
		private $tamanho;
		private static $tamanhosPossiveis = array('PP','P', 'M', 'G', 'GG', 'XGG');

		public function verificaTamanho($valor){
			if (!in_array($valor, $tamanhosPossiveis)) {
				error_log ("Tamanho incompatível." . $valor, 0);
			}
		}				
		
		public function tamanhoCamisa($valor){
			$this->setTamanhoCamisa($valor);
		}
		
		public function getTamanhoCamisa(){
			return $this->tamanho;
		}
		
		public function setTamanhoCamisa($valor){
			$this->verificaTamanho($valor);
			$this->tamanho = $valor;
		}
	}
?>