<?
include('peach.php');
$Peach = new Peach();
$String = 'lol! look out below, guys';
echo ($Peach->String($String)->Replace("LOL", "i am amused", Stems::CaseInsensitive));
