<?php
if (!defined('APPLICATION')) exit;

$page = $_GET["page"];
$gotoPage = $page * 20;
$data = "";

if($page != ""){
  $filename = "lists/".$page.".txt";
}else{
  $filename = "lists/0.txt";
}
if (file_exists($filename)) {
      $data = file_get_contents($filename);
      echo ("<br />");
  } else { 
    if($page == ""){
      $data = file_get_contents("http://pokeapi.co/api/v2/pokemon/");
      $page = 0;
      if($data != ""){
        file_put_contents ( $filename , $data);
      }
    }
    else{
      $data = file_get_contents("http://pokeapi.co/api/v2/pokemon/?offset=$gotoPage");
      if($data != ""){
        file_put_contents ( $filename , $data);
      }
    }
}
$rData = json_decode($data, true);
if($rData != ""){
echo ("<table>");
$count = 0;
while($count != count($rData['results'])){
     echo ("<tr><th>Pokemon : &nbsp;</th><td> " . $rData['results'][$count]['name'] . "</td><td><a href='result.php?nameFromList=".$rData['results'][$count]['name']."' class='buttonBlah'>Go To Entry</a></td></tr>");
     $count++;
    }
$nextPage = $page++;
$nextPage++;

$previousPage = $page--;
$previousPage--;
$previousPage--;
  
  
echo ("</table><br>");
if($previousPage > -1){
  echo ("<a href='list.php?page=$previousPage' class='buttonBlah'>Previous</a> - ");
}
echo ("<a href='list.php?page=$nextPage' class='buttonBlah'>Next</a><br><br>");
}
else{
  echo ("Could not get list");
}
?>