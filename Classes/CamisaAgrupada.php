<?php
include_once 'Camisa.php';

class CamisaAgrupada extends Camisa{
    private $listaCamisas;

    function CamisaAgrupada(Camisa $camisa){
        $this->copiaDados($camisa);
        $this->setTamanho("");
        $this->setCodigoBarra("");
        $this->setCodFornecedor("");
        $this->setSaldo("");
        $this->setDescricao("");
        $this->setCodigo("G_" . $this->getTipoModelo() . "_" . str_replace(" ", "_", $this->getDescricaoResumida()));
    }

    public function addListaCamisa(Camisa $camisa){
        $this->listaCamisas[] = $camisa->getCodigo();
        $camisa->setTemGrupo(true);
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

    public function getListaSkus(){
        $codigoCamisas = "";
        foreach($this->listaCamisas as $codCamisaAtual){
            if ($codigoCamisas != "")
                $codigoCamisas .= ",";

            $codigoCamisas .= $codCamisaAtual;
        }
        return $codigoCamisas;
    }

    public function isSimpleProduct(){
        return false;
    }

}
