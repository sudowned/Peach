<?
include('peach.php');
$String = Peach::String('lol! look out below, guys');
$String2 = Peach::String('i guarantee this breaks it');
$String = $String->Replace("LOL", "i am amused", false);
echo("\nbirds: ".$String->Contains('amused')."\n");
echo($String);
echo("\nbees: ");
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
