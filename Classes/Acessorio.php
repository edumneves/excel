<?php
include_once 'ItemEstoque.php';

class Acessorio extends ItemEstoque
{
    public function configuraItem()
    {

    }

    public static function valido($item)
    {
        if (!ItemEstoque::valido($item))
            return false;
        return true;
    }
}


?>