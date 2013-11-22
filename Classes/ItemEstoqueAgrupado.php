<?php
include_once 'ItemEstoque.php';

class ItemEstoqueAgrupado extends ItemEstoque {
    private $listaItens;

    public function addListaItens(ItemEstoque $item){
        $listaItens[] = $item->getCodigo();
    }

    public function configuraItem()
    {
        // TODO: Implement configuraItem() method.
    }

}
