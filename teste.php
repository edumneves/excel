<html>
<head></head>
<body>

<?php

function binary_search(array $a, $first, $last, $key)
{
    $lo = $first;
    $hi = $last - 1;

    while ($lo <= $hi) {
        $mid = (int)(($hi - $lo) / 2) + $lo;
        $cmp = strcmp($a[$mid], $key);

        if ($cmp < 0) {
            $lo = $mid + 1;
        } elseif ($cmp > 0) {
            $hi = $mid - 1;
        } else {
            return $mid;
        }
    }
    return -($lo + 1);

}

?>
<?php



$listaBandas = array(
    "ACDC" => "AC/DC",
    "ALICE IN CHAINS" => "Alice in Chains",
    "AMON AMARTH" => "Amon Amarth",
    "AMY" => "Amy Winehouse",
    "ANGRA" => "Angra",
    "ANTHRAX" => "Anthrax",
    "ARTIC MONKEYS" => "Artic Monkeys",
    "BLACK SABBATH" => "Black Sabbath",
    "METALLICA" => "Metallica",
    "BAD RELIGION" => "Bad Religion",
    "PINK FLOYD" => "Pink Floyd",
    "RAMONES" => "Ramones",
    "STONES" => "Rolling Stones",
    "PASSARO VERMELHO" => "Passaro Vermelho",
    "RED HOT" => "Red Hot Chili Peppers",
    "BEATLES" => "Beatles",
    "JACK DANIELS" => "Jack Daniels",
    "V DE VINGANCA" => "V de Vingança",
    "BAZINGA" => "The Big Bang Theory",
    "NIRVANA" => "Nirvana",
    "LARANJA MECANICA" => "Laranja Mecânica",
    "GAMES" => "Games",
    "SLAYER" => "Slayer",
    "GHOST" => "Ghost",
    "JUDAS" => "Judas",
    "WASP" => "Wasp",
    "SARCOFAGO" => "Sarcofago",
    "EXODUS" => "Exodus",
    "KREATOR" => "Kreator",
    "OBITUARY" => "Obituary",
    "MEGADETH" => "Megadeth",
    "DEEP PURPLE" => "Deep Purple",
    "PANTERA" => "Pantera",
    "OZZY" => "Ozzy Osbourne",
    "SCORPIONS" => "Scorpions",
    "YES" => "Yes",
    "KISS" => "Kiss",
    "BEATELS" => "Beatles",
    "DIO" => "Dio",
    "BACK SABBATH" => "Black Sabbath",
    "RAINBOW" => "Rainbow",
    "TESTAMENT" => "Testament",
    "VENON" => "Venon",
    "DEICEIDE" => "Deicide",
    "DEICIDE" => "Deicide",
    "DESTRUCTION" => "Destruction",
    "SEPULTURA" => "Sepultura",
    "BLACK ANGEL" => "Black Angel",
    "MERCYFUL FATE" => "Mercyful Fate",
    "HELLOWEEN" => "Helloween",
    "DRI" => "DRI",
    "MORBID ANGEL" => "Morbid Angel",
    "LYNYRD" => "Lynyrd Skynyrd",
    "HARLEY" => "Harley-Davidson",
    "SEX PISTOLS" => "Sex Pistols",
    "VAN HALEN" => "Van Halen",
    "ENGENHEIROS" => "Engenheiros do Hawaii",
    "JETHRO TULL" => "Jethro Tull",
    "BLACK LABEL SOCIETY" => "Black Label Society",
    "SMITHS" => "Smiths",
    "SONIC YOUTH" => "Sonic Youth",
    "PATERA" => "Pantera",
    "THE WHO" => "The Who",
    "STAR WARS" => "Star Wars",
    "GIBSON" => "Guitarra",
    "FENDER" => "Guitarra",
    "PODEROSO CHEFAO" => "O Poderoso Chefão",
    "HEINEKEN" => "Heineken",
    "MISFITS" => "Misfits",
    "THE STROKES" => "The Strokes",
    "IRON" => "Iron Maiden",
    "LED ZEPPELING" => "Led Zeppeling",
    "MOTORHEAD" => "Motorhead",
    "MARSHALL" => "Guitarras",
    "EVANESCENCE" => "Evanescence",
    "LEGIAO" => "Legião Urbana",
    "SYSTEM" => "System Of A Down",
    "GREEN DAY" => "Green Day"
);
//	$camisa = "CAMISA CM XGG BLACK SABBATH HEAVEN";
//	$codigo = "CMCMXGG00007";

//	$camisa = "CAMISA CM XGG ACDC HELLS BELLS";
//	$codigo = "CMCMXGG00006";   

$camisa = "CAMISA CM PP STONES LINGUA PRETA";
$codigo = "CMCMPP00007";

$descricao = $camisa;

$tamCodigo = strlen($codigo);

$tipoItem = substr($codigo, 0, 2);
$tipoModelo = substr($codigo, 2, 2);
$tamanho = substr($codigo, 4, $tamCodigo - 9);

ksort($listaBandas);
$listaChaves = array_keys($listaBandas);

$descricao = str_replace("CAMISA " . $tipoModelo . " " . $tamanho . " ", "", $descricao);
$palavras = explode(" ", $descricao);
$quantPalavras = count($palavras);
$nomeSimples = $palavras[0];
if ($quantPalavras > 0)
    $nomeDuplo = $nomeSimples . " " . $palavras[1];
if ($quantPalavras > 1)
    $nomeTriplo = $nomeDuplo . " " . $palavras[2];
echo "Nome simples = " . $nomeSimples . " Nome duplo = " . $nomeDuplo . " Nome Triplo = " . $nomeTriplo . "<br>";

$nome = $nomeTriplo;
$idx = binary_search($listaChaves, 0, sizeof($listaChaves), $nome);
if ($idx >= 0) {
    echo " ACHEI = " . $nome . " em " . $idx . " Descrição = " . $listaBandas[$listaChaves[$idx]] . "<br>";
} else {
    $nome = $nomeDuplo;
    $idx = binary_search($listaChaves, 0, sizeof($listaChaves), $nome);
    if ($idx >= 0) {
        echo " ACHEI = " . $nome . " em " . $idx . " Descrição = " . $listaBandas[$listaChaves[$idx]] . "<br>";
    } else {
        $nome = $nomeSimples;
        $idx = binary_search($listaChaves, 0, sizeof($listaChaves), $nome);
        if ($idx >= 0) {
            echo " ACHEI = " . $nome . " em " . $idx . " Descrição = " . $listaBandas[$listaChaves[$idx]] . "<br>";
        } else {
            error_log("Não encontrei a banda da camisa " . $codigo . " Descrição = " . $descricao);
        }

    }
}

?>
</body>
</html>