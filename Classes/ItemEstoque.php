<?php
	include_once 'ImportacaoGlobal.php';

	abstract class ItemEstoque {
		private $codigo;
		private $codigoBarra;
		private $descricao;
		private $codFornecedor;
		private $refFornecedor;
		private $precoCusto;
		private $precoVenda;
		private $saldo;
		private $saldoPeso;


		public function ItemEstoque($item){
			$this->setCodigo(ImportacaoGlobal::extraiCodigo($item));
			$this->setCodigoBarra(ImportacaoGlobal::extraiCodigoBarra($item));
			$this->setDescricao(ImportacaoGlobal::extraiDescricao($item));
			$this->setCodFornecedor(ImportacaoGlobal::extraiCodFornecedor($item));
			$this->setRefFornecedor(ImportacaoGlobal::extraiRefFornecedor($item));
			$this->setPrecoCusto(ImportacaoGlobal::extraiPrecoCusto($item));
			$this->setPrecoVenda(ImportacaoGlobal::extraiPrecoVenda($item));
			$this->setSaldo(ImportacaoGlobal::extraiSaldo($item));
			$this->setSaldoPeso(ImportacaoGlobal::extraiSaldoPeso($item));
			$this->configuraItem();
		}
		
		// Função que vai fazer toda a configuração do item
		abstract public function configuraItem();

		// Função que verifica se o item é válido, ou seja, se não está com referência
		// incorretas. Ex: Camisa com código de Acessório
		public static function valido(array $item){
		
			// Confere o fornecedor
            $codFornecedor = ImportacaoGlobal::extraiCodFornecedor($item);
			if (strcmp("BEM", $codFornecedor) == 0) {
				error_log("Cod Fornecedor incorreto!" . $codFornecedor);
				return false;
			}
			
			// Confere preco de venda
            $precoVenda = ImportacaoGlobal::extraiPrecoVenda($item);
			if ($precoVenda <= 0) {
				error_log ("Preco venda incorreto!" . $precoVenda);
				return false;
			}

			// Confere o preço de custo
            $precoCusto = ImportacaoGlobal::extraiPrecoCusto($item);
			if ($precoCusto < 0) {
				error_log ("Preco custo incorreto!" . $precoCusto);
				return false;
			}
			
			// Confere o estoque
			/*
			if ($this->getSaldo() < 0) {
				echo "Saldo incorreto!" . $this->getSaldo();
				return false;
			}
			*/
			return true;
		}


		public function getCodigo() {
			return $this->codigo;
		}
		
		public function setCodigo($valor) {
			$this->codigo = trim($valor);
		}

		public function getCodigoBarra() {
			return $this->codigoBarra;
		}
		
		public function setCodigoBarra($valor) {
			$this->codigoBarra = trim($valor);
		}

		public function getDescricao() {
			return $this->descricao;
		}
		
		public function setDescricao($valor) {
			$this->descricao = trim($valor);
		}

		public function getCodFornecedor() {
			return $this->codFornecedor;
		}
		
		public function setCodFornecedor($valor) {
			$this->codFornecedor = trim($valor);
		}

		public function getRefFornecedor() {
			return $this->refFornecedor;
		}
		
		public function setRefFornecedor($valor) {
			$this->refFornecedor = trim($valor);
		}

		public function getPrecoCusto() {
			return $this->precoCusto;
		}
		
		public function setPrecoCusto($valor) {
			$this->precoCusto = trim($valor);
		}

		public function getPrecoVenda() {
			return $this->precoVenda;
		}

		public function setPrecoVenda($valor) {
			$this->precoVenda = trim($valor);
		}

		public function getSaldo() {
			return $this->saldo;
		}

		public function setSaldo($valor) {
			$this->saldo = trim($valor);
		}

		
		public function getSaldoPeso() {
			return $this->saldoPeso;
		}

		public function setSaldoPeso($valor) {
			$this->saldoPeso = trim($valor);
		}


	}
?>


/*
 *
    [1] => Array
    (
        [A] => CODIGO
        [B] => CODIGO BARRA
        [C] => DESCRICAO
        [D] => COD.FORNECEDOR
        [E] => REF.FORNECEDOR
        [F] => PRECO CUSTO
        [G] => PRECO VENDA
        [H] => SALDO
        [I] => SALDO PESO
    )

    [2] => Array
    (
        [A] => ACIMASM00001
        [B] => 52741
        [C] => ACESSORIOS IMAS MEDIOS BANDAS
        [D] =>
        [E] =>
        [F] => 3
        [G] => 6
        [H] => 0
        [I] => 0
    )
*/