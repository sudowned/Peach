<?
include('peach.php');
$Peach = new Peach();
$String = $Peach->String('lol! look out below, guys');
echo ($String->Replace("LOL", "i am amused", false));
echo("\n");
echo($String->Contains('lol'));
echo("\n");
echo($String);
echo("\n");
echo($String->Split(' ')->Contains('look'));
echo("\n");
/*
// Test for memory leaks. If uncommenting this block causes
// increasing memory consumption, there's a reference to the
// temp objects being left open.
while(1){
	$String->Contains('esdfsdgsgd');
}
*/
