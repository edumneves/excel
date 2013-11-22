<?php
include_once 'Camisa.php';

class CamisaAgrupada extends Camisa{
    private $listaCamisas;

    function CamisaAgrupada(Camisa $camisa){
        $this->copiaDados($camisa);
        $this->setTamanho("");
    }

    public function addListaCamisa(Camisa $camisa){
        $listaCamisas[] = $camisa->getCodigoBarra();
    }

    public function configuraItem()
    {
        // TODO: Implement configuraItem() method.
    }
}
