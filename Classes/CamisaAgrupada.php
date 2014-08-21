<?php
include_once 'Camisa.php';

class CamisaAgrupada extends Camisa
{
    private $listaCamisas;
    private $codImagem = "";

    function CamisaAgrupada(Camisa $camisa)
    {
        $this->copiaDados($camisa);
        $this->setTamanho("");
        $this->setCodigoBarra("");
        $this->setRefFornecedor("");
        $this->setSaldo("");
        $this->setDescricao("");
        $this->setCodigo("G_" . $this->getTipoModelo() . "_" . str_replace(" ", "_", $this->getDescricaoResumida()));
    }

    public function addListaCamisa(Camisa $camisa)
    {
        $this->listaCamisas[] = $camisa->getCodigo();
        $camisa->setTemGrupo(true);

        // Código de barras é da camisa M ou de 2A no caso de infantil
        if ($this->codImagem == "") {
            $this->codImagem = $camisa->getCodigoBarra();
        } else {
            if ($this->getTipoModelo() == "IN" && $camisa->getTamanho() == "2A") {
                $this->codImagem = $camisa->getCodigoBarra();
            } else if ($this->getTipoModelo() == "CM" && $camisa->getTamanho() == "M") {
                $this->codImagem = $camisa->getCodigoBarra();
            }
        }
    }

    public function getCodImagem()
    {
        return $this->codImagem;
    }

    public function configuraItem()
    {
        // TODO: Implement configuraItem() method.
    }

    function __toString()
    {
        $retorno = parent::__toString();

        $codigoCamisas = $this->getListaSkus();
        return $retorno . $codigoCamisas;
    }

    public function getListaSkus()
    {
        $codigoCamisas = "";
        foreach ($this->listaCamisas as $codCamisaAtual) {
            if ($codigoCamisas != "")
                $codigoCamisas .= ",";

            $codigoCamisas .= $codCamisaAtual;
        }
        return $codigoCamisas;
    }

    public function isSimpleProduct()
    {
        return false;
    }

}
