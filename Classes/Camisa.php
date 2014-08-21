<?php
	include_once 'ItemEstoque.php';
	include_once 'ImportacaoGlobal.php';
	
	class Camisa extends ItemEstoque {
		private $tamanho; // PP, P, M, G, GG, XGG
		private $banda;
		private $tipoModelo; // Camisa = CM, Camiseta, Baby look = BL, BY = Body, IN = Infantil, MC = Manga Caida


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

            // Confere o tipo modelo
            $tipoModelo = substr($codigo, 2, 2);
            $tiposValidos = array("BL", "CM", "BY", "IN", "MC");
            if (!in_array($tipoModelo, $tiposValidos)){
                error_log ("Tipo Modelo invalido ! Codigo = " . $codigo);
                return false;
            }
            return true;
        }

        private function getTipoModeloExtensoOriginal($tipoModelo){
            switch ($tipoModelo){
                case "BL":
                    return ("BABY");
                case "CM":
                    return ("CM");
                case "BY":
                    return ("BODY");
                case "IN":
                    return ("INFANTIL");
                case "MC":
                    return ("MANGA CAIDA");
            }
        }

        private function getTamanhoOriginal($tipoModelo, $tamanho) {
            switch($tipoModelo){
                case "IN":
                    switch($tamanho){
                        case "2A":
                            return "2 ANOS";
                        case "4A":
                            return "4 ANOS";
                        case "6A":
                            return "6 ANOS";
                        case "8A":
                            return "8 ANOS";
                    }
                    break;
            }
            return $tamanho;
        }

        private function getDefinicao(){
            global $listaBandas;
            global $listaChaves;

            $codigo = $this->getCodigo();
            $tamCodigo = strlen($codigo);

            $tipoModelo = substr($codigo, 2, 2);
            $tamanho = substr($codigo, 4, $tamCodigo -9);

            $tamanho = $this->getTamanhoOriginal($tipoModelo, $tamanho);

            $descricao = $this->getDescricao();

            $tipoModeloExtenso = $this->getTipoModeloExtensoOriginal($tipoModelo);

            $descricao = trim(str_replace("CAMISA " . $tipoModeloExtenso . " " . $tamanho . " ", "", $descricao));


            // @todo Retirar isso quando estiver correto o cadastro das Mangas Caídas (Golas Caídas)
            $descricao = trim(str_replace("CAMISA GOLA CAIDA " . $tamanho . " ", "", $descricao));


            $descricao = trim(str_replace("CAMISA", "", $descricao));
            $descricao = trim(str_replace(" " . $tipoModeloExtenso, "", $descricao));


            $descricao = trim(str_replace(" " . $tamanho . " ", "", $descricao));

            $this->setDescricaoResumida($descricao);

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
            $idx_search = binary_search($listaChaves, 0, sizeof($listaChaves), $nomeTriplo);
            if ($idx_search >= 0)
                return ($listaBandas[$listaChaves[$idx_search]]);

            $idx_search = binary_search($listaChaves, 0, sizeof($listaChaves), $nomeDuplo);
            if ($idx_search >= 0)
                return ($listaBandas[$listaChaves[$idx_search]]);

            $idx_search = binary_search($listaChaves, 0, sizeof($listaChaves), $nomeSimples);
            if ($idx_search >= 0)
                return ($listaBandas[$listaChaves[$idx_search]]);

            // Caso não tenha encontrado a banda
            echo "<br>Descricao = " . $descricao . "<br>";
            echo "Descricao Original = " . $this->getDescricao() . "<br>";
            echo "Codigo Original = " . $this->getCodigo() . "<br>";
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

            $vetorCamisa = explode(" ", $this->getDescricao());
            $tamanhoDescricao = $vetorCamisa[2];

            if ($tamanhoDescricao != $tamanho){
                error_log("Camisa com tamanhos diferentes. Tamanho código = " . $tamanho . " Tamnho descrição = " . $tamanhoDescricao . " Código = " . $codigo);
            }

            $definicao = $this->getDefinicao();
            if ($definicao != null) {
                $this->setBanda($definicao["Banda"]);
                $this->setCategoria($definicao["Categoria"]);
            } else {
                error_log("Camisa sem definição. Código = " . $codigo);
            }
		}


		public function getTamanho(){
			return $this->tamanho;
		}
		public function getBanda(){
			return $this->banda;
		}
		public function getTipoModelo(){
			return $this->tipoModelo;
		}
		public function getTipoModeloExtenso(){
			switch ($this->tipoModelo){
				case "CM":
					return "com manga";
				case "BL":
					return "Baby Look";
                case "BY":
                    return "Body";
                case "MC":
                    return "Gola Caída";
                case "IN":
                    return "Infantil";
			}
			return "";
		}

		public function setTamanho($valor){
			$this->tamanho = trim($valor);
		}
		public function setTipoModelo($valor){
			$this->tipoModelo = trim($valor);
		}
		public function setBanda($valor){
			$this->banda = trim($valor);
		}

        public function feminina(){
            return (strcmp($this->getTipoModelo(), "BL")==0);
        }

        public function masculina(){
            return !feminina();
        }

		public function __toString(){
			$retorno = "Camisa " . ";";
            $retorno .= $this->getTipoModeloExtenso() . ";";
            $retorno .= $this->getDescricaoResumida() . ";";
			$retorno .= $this->getTamanho() . ";";
			$retorno .= $this->getBanda() . ";";
            $retorno .= $this->getCor() . ";";
            $retorno .= $this->getTitulo() . ";";
            $retorno .= $this->getDescricao() . ";";
			$retorno .= $this->getCodFornecedor() . ";";
			$retorno .= $this->getRefFornecedor() . ";";
			$retorno .= $this->getPrecoCusto() . ";";
			$retorno .= $this->getPrecoVenda() . ";";
			$retorno .= $this->getSaldo() . ";";
            $retorno .= $this->getCodigo() . ";";
            $retorno .= $this->getTemGrupo() . ";";

			return $retorno;
		}

        public function copiaDados(Camisa $camisa)
        {
            $this->setBanda($camisa->getBanda());
            $this->setTipoModelo($camisa->getTipoModelo());
            $this->setTamanho($camisa->getTamanho());

            // Tem que ser no final, pois já tem que estar com o Tipo de Modelo definido para ficar
            // com o título correto
            parent::copiaDados($camisa);
        }

        public function setDescricaoResumida($descricaoResumida)
        {
            parent::setDescricaoResumida($descricaoResumida);

//            $titulo = "Camisa ";
//            $titulo .= $this->getTipoModeloExtenso() . " ";
//            $titulo .= ucwords(strtolower($descricaoResumida));

            $titulo = ucwords(strtolower($descricaoResumida));

            $this->setTitulo($titulo);
        }

        public function getCodImagem(){
            return $this->getCodigoBarra();
        }


    }
	
?>