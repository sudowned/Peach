<?
include('peach.php');
$Peach = new Peach();
$String = $Peach->String('lol! look out below, guys');
echo ($String->Replace("LOL", "i am amused", Stems::CaseInsensitive));
