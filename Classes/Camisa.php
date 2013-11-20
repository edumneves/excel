<?php
	include_once 'ItemEstoque.php';
	include_once 'ImportacaoGlobal.php';
	
	class Camisa extends ItemEstoque {
		private $tamanho; // PP, P, M, G, GG, XGG
		private $cor;
		private $banda;
		private $categoria;
		private $tipoModelo; // Camisa = CM, Camiseta, Baby look = BL

        public static function valido($item){
            if (!ItemEstoque::valido($item))
                return false;

            $codigo = ImportacaoGlobal::extraiCodigo($item);
            $tipoItem = substr($codigo, 0, 2);
            if (strcmp($tipoItem, "CM")) {
                error_log ("Codigo incorreto!" . $codigo);
                return false;
            }

            // Confere a descrição
            $descricao = ImportacaoGlobal::extraiDescricao($item);
            if (strpos($descricao, "CAMISA") === false){
                error_log ("Descricao incorreta!" . $descricao);
                return false;
            }
            return true;
        }

        private function getDefinicao(){
            global $listaBandas;
            global $listaChaves;

            $codigo = $this->getCodigo();
            $tamCodigo = strlen($codigo);

            $tipoModelo = substr($codigo, 2, 2);
            $tamanho = substr($codigo, 4, $tamCodigo -9);


            $descricao = $this->getDescricao();
            if ($tipoModelo == "BL")
                $tipoModeloExtenso = "BABY";
            else
                $tipoModeloExtenso = "CM";

            $descricao = trim(str_replace("CAMISA " . $tipoModeloExtenso . " " . $tamanho . " ", "", $descricao));
            $descricao = trim(str_replace("CAMISA ", "", $descricao));
            $descricao = trim(str_replace($tipoModeloExtenso . " " , "", $descricao));
            $descricao = trim(str_replace($tamanho . " ", "", $descricao));

            // Monta as formas possíveis de nome de banda para buscar
            $palavras = explode(" ", $descricao);
            $quantPalavras = count($palavras);
            $nomeSimples = $palavras[0];
            $nomeDuplo = "";
            $nomeTriplo = "";
            if ($quantPalavras > 0 && isset($palavras[1])) {
                $nomeDuplo = $nomeSimples . " " . $palavras[1];
                if ($quantPalavras > 1 && isset($palavras[2]))
                    $nomeTriplo = $nomeDuplo . " " . $palavras[2];
            }

            // Faz as buscas pelos nomes simples, duplo e triplo
            $idx = binary_search($listaChaves, 0, sizeof($listaChaves), $nomeTriplo);
            if ($idx >= 0)
                return ($listaBandas[$listaChaves[$idx]]);

            $idx = binary_search($listaChaves, 0, sizeof($listaChaves), $nomeDuplo);
            if ($idx >= 0)
                return ($listaBandas[$listaChaves[$idx]]);

            $idx = binary_search($listaChaves, 0, sizeof($listaChaves), $nomeSimples);
            if ($idx >= 0)
                return ($listaBandas[$listaChaves[$idx]]);

            // Caso não tenha encontrado a banda
            echo "<br>Descricao = " . $descricao . "<br>";
            echo "TipoModelo = " . $tipoModelo . "<br>";
            echo "Replace = " . "CAMISA " . $tipoModeloExtenso . " " . $tamanho . " " . "<br>";
            echo "Nome simples " . $nomeSimples . "<br>";
            echo "Nome Duplo " . $nomeDuplo . "<br>";
            echo "Nome Triplo " . $nomeTriplo . "<br>";
            echo("Não encontrei a banda da camisa " . $codigo . " Descrição = " . $descricao . "<br/><br/>");
            return (null);
        }
		
		public function configuraItem(){
		

			$codigo = $this->getCodigo();
			$tamCodigo = strlen($codigo);
						
			$tipoModelo = substr($codigo, 2, 2);
			$tamanho = substr($codigo, 4, $tamCodigo -9);
			
			$this->setTipoModelo($tipoModelo);
			$this->setTamanho($tamanho);
			
            $definicao = $this->getDefinicao();
            if ($definicao != null) {
                $this->setBanda($definicao["Banda"]);
            } else {
                error_log("Camisa sem definição. Código = " . $codigo);
            }
		}


		public function getTamanho(){
			return $this->tamanho;
		}
		public function getCor(){
			return $this->cor;
		}
		public function getBanda(){
			return $this->banda;
		}
		public function getCategoria(){
			return $this->categoria;
		}
		public function getTipoModelo(){
			return $this->tipoModelo;
		}
		public function getTipoModeloExtenso(){
			switch ($this->tipoModelo){
				case "CM":
					return "Com manga";
				case "BL":
					return "Baby Look";
			}
			return "";
		}

		public function setTamanho($valor){
			$this->tamanho = trim($valor);
		}
		public function setTipoModelo($valor){
			$this->tipoModelo = trim($valor);
		}
		public function setCor($valor){
			$this->cor = trim($valor);
		}
		public function setBanda($valor){
			$this->banda = trim($valor);
		}
		public function setCategoria($valor){
			$this->categoria = trim($valor);
		}
		
		public function __toString(){
			$retorno = "Camisa " . $this->getTipoModeloExtenso();
			$retorno .= " Descricao = " . $this->getDescricao();
			$retorno .= " Tamanho = " . $this->getTamanho();
			$retorno .= " Cor = " . $this->getCor();
			$retorno .= " Banda = " . $this->getBanda();
			$retorno .= " Cod Fornecedor = " . $this->getCodFornecedor();
			$retorno .= " Ref Fornecedor = " . $this->getRefFornecedor();
			$retorno .= " Preco Custo = " . $this->getPrecoCusto();
			$retorno .= " Preco Venda = " . $this->getPrecoVenda();
			$retorno .= " Saldo Estoque = " . $this->getSaldo();

			return $retorno;			
		}
	}
	
?>