<?php
include_once 'ImportacaoGlobal.php';

abstract class ItemEstoque
{
    private $codigo;
    private $codigoBarra;
    private $descricao;
    private $codFornecedor;
    private $refFornecedor;
    private $precoCusto;
    private $precoVenda;
    private $saldo;
    private $saldoPeso;
    private $categoria;
    private $cor;
    private $titulo;
    private $temGrupo = false;

    public function setTemGrupo($temGrupo)
    {
        $this->temGrupo = $temGrupo;
    }

    public function getTemGrupo()
    {
        return $this->temGrupo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    private $descricaoResumida;

    public function setDescricaoResumida($descricaoResumida)
    {
        $this->descricaoResumida = $descricaoResumida;
    }

    public function getDescricaoResumida()
    {
        return $this->descricaoResumida;
    }

    public function setCor($valor)
    {
        $this->cor = trim($valor);
    }

    public function getCor()
    {
        return $this->cor;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($valor)
    {
        $this->categoria = trim($valor);
    }

    public function ItemEstoque($item)
    {


        $this->setCodigo(ImportacaoGlobal::extraiCodigo($item));
        $this->setCodigoBarra(ImportacaoGlobal::extraiCodigoBarra($item));
        $this->setDescricao(ImportacaoGlobal::extraiDescricao($item));
        $this->setCodFornecedor(ImportacaoGlobal::extraiCodFornecedor($item));
        $this->setRefFornecedor(ImportacaoGlobal::extraiRefFornecedor($item));
        $this->setPrecoCusto(ImportacaoGlobal::extraiPrecoCusto($item));
        $this->setPrecoVenda(ImportacaoGlobal::extraiPrecoVenda($item));
        $this->setSaldo(ImportacaoGlobal::extraiSaldo($item));
        $this->setSaldoPeso(ImportacaoGlobal::extraiSaldoPeso($item));

        $this->setTitulo($this->getDescricao());

        $this->configuraItem();

        echo "Codigo = " . ImportacaoGlobal::extraiCodigo($item);
        echo " CodigoBarra = " . $this->getCodigoBarra();
        echo " Descricao = " . $this->getDescricao();
        echo " Cod Fornecedor = " . $this->getCodFornecedor();
        echo " Ref Fornecedor = " . $this->getRefFornecedor();
        echo " Saldo = " . $this->getSaldo();
        print_r($item);
        echo " <br/> ";

    }

    // Função que vai fazer toda a configuração do item
    abstract public function configuraItem();

    // Função que verifica se o item é válido, ou seja, se não está com referência
    // incorretas. Ex: Camisa com código de Acessório
    public static function valido(array $item)
    {

        // Confere o fornecedor
        $codFornecedor = ImportacaoGlobal::extraiCodFornecedor($item);
        if (strcmp("BEM", $codFornecedor) == 0) {
            error_log("Cod Fornecedor incorreto!" . $codFornecedor);
            return false;
        }

        // Confere preco de venda
        $precoVenda = ImportacaoGlobal::extraiPrecoVenda($item);
        if ($precoVenda == "" || $precoVenda == 0)
            $precoVenda = PRECO_VENDA;

        if ($precoVenda < 0) {
            error_log("Preco venda incorreto!" . $precoVenda);
            return false;
        }

        // Confere o preço de custo
        $precoCusto = ImportacaoGlobal::extraiPrecoCusto($item);
        if ($precoCusto == "")
            $precoCusto = PRECO_CUSTO;

        if ($precoCusto < 0) {
            error_log("Preco custo incorreto!" . $precoCusto);
            return false;
        }

        // Confere o estoque, não cadastra itens sem estoque
        $saldo = ImportacaoGlobal::extraiSaldo($item);
        if ($saldo == "")
            $saldo = 0;

        if ($saldo <= 0) {
            error_log("Saldo incorreto!" . $saldo);
            // return false;

            // Zera saldo quando está negativo
            $saldo = 0;
        }
        return true;
    }


    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($valor)
    {
        $this->codigo = trim($valor);
    }

    public function getCodigoBarra()
    {
        return $this->codigoBarra;
    }

    public function setCodigoBarra($valor)
    {
        $this->codigoBarra = trim($valor);
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($valor)
    {
        $this->descricao = trim($valor);
    }

    public function getCodFornecedor()
    {
        return $this->codFornecedor;
    }

    public function setCodFornecedor($valor)
    {
        $this->codFornecedor = trim($valor);
    }

    public function getRefFornecedor()
    {
        return $this->refFornecedor;
    }

    public function setRefFornecedor($valor)
    {
        $this->refFornecedor = trim($valor);
    }

    public function getPrecoCusto()
    {
        return $this->precoCusto;
    }

    public function setPrecoCusto($valor)
    {
        $this->precoCusto = trim($valor);
    }

    public function getPrecoVenda()
    {
        return $this->precoVenda;
    }

    public function setPrecoVenda($valor)
    {
        $this->precoVenda = trim($valor);
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function setSaldo($valor)
    {
        $this->saldo = trim($valor);
    }


    public function getSaldoPeso()
    {
        return $this->saldoPeso;
    }

    public function setSaldoPeso($valor)
    {
        $this->saldoPeso = trim($valor);
    }

    public function copiaDados(ItemEstoque $item)
    {
        $this->setCodigo($item->getCodigo());
        $this->setCategoria($item->getCategoria());
        $this->setCodigoBarra($item->getCodigoBarra());
        $this->setDescricao($item->getDescricao());
        $this->setCodFornecedor($item->getCodFornecedor());
        $this->setRefFornecedor($item->getRefFornecedor());
        $this->setPrecoCusto($item->getPrecoCusto());
        $this->setPrecoVenda($item->getPrecoVenda());
        $this->setSaldo($item->getSaldo());
        $this->setSaldoPeso($item->getSaldoPeso());
        $this->setDescricaoResumida($item->getDescricaoResumida());
        $this->setTitulo($this->getTitulo());

    }


    public function isSimpleProduct()
    {
        return true;
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