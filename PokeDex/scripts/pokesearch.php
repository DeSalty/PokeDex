<?php
if (!defined('APPLICATION')) exit;
  if($_GET["nameFromList"] != "")
  {
    $name = $_GET["nameFromList"];
  }
  $name = strtolower($name);
  $image = "";
  $filename = "pokemon/".$name.".txt";
  $entryfile = "pokemon/entry".$name.".txt";
  $cachetime = 86400;
  if (file_exists($filename) && (time() - $cachetime < filemtime($filename))) {
      $data = file_get_contents($filename);
      $image = "sprite/".$name . ".jpg";
      echo ("<br />");
  } else {
      $data = file_get_contents("http://pokeapi.co/api/v2/pokemon/".$name);
      if($data != ""){
        file_put_contents ( $filename , $data);
        $tempData = json_decode($data, true);
        $input = $tempData['sprites']['front_default'];
        $output = "sprite/".$name.'.jpg';
        file_put_contents($output, file_get_contents($input));
        $image = $output;
      }
  }

if(file_exists($entryfile) && (time() - $cachetime < filemtime($entryfile))){
  $entrydata = file_get_contents($entryfile);
} else {
  $entrydata = file_get_contents("http://pokeapi.co/api/v2/pokemon-species/".$name);
  if($entrydata != ""){
    file_put_contents( $entryfile, $entrydata);
  }
}



?>
<style>
    img.swell:hover {
    transform:scale(3.3,3.3);
    -ms-transform:scale(3.3,3.3);
    -webkit-transform:scale(3.3,3.3);}
</style>
<?php
  if($data != ""){
    $rData = json_decode($data, true);
    if($entrydata!= ""){
      $rEntry = json_decode($entrydata, true);
    }
    //Getting name and ID; Works
    if($image != ""){
      echo("<img src='". $image ."' class='swell'></img><br />");
    }
    else{
        echo ("<img src='" . $rData['sprites']['front_default']."' class='swell'></img><br />");
    }
    echo ("Found a pokemon with Name: " . $rData['name'] . "<br />");
    echo ("PokeDex entry number is : " . $rData['id'] . "<br /><br />");
    if($rEntry != ""){
      echo ($rEntry['flavor_text_entries'][1]['flavor_text']);
      echo("<br><br>");
    //<evolution>
      
      $evoFile = "pokemon/evo". $name . ".txt";
      if(file_exists($evoFile) && (time() - $cachetime < filemtime($evoFile))){
        $evolution = file_get_contents($evoFile);
      }else{
        $evolution = file_get_contents($rEntry['evolution_chain']['url']);
        if($evolution != ""){
          file_put_contents($evoFile, $evolution);
        }
      }
      $rEvo = json_decode($evolution, true);
      if($rEvo != ""){
        $name1 = $rEvo['chain']['species']['name'];
        $name2 = $rEvo['chain']['evolves_to'][0]['species']['name'];
        $name3 = $rEvo['chain']['evolves_to'][0]['evolves_to'][0]['species']['name'];
        if($rEvo['chain']['evolves_to'][0]['species'] != ""){
          echo ("<a href='result.php?nameFromList=".$name1 . "'>".$name1. "</a> evolves ");
          echo (" into " . "<a href='result.php?nameFromList=".$name2 . "'>".$name2 . "</a>");
          
          $level = $rEvo['chain']['evolves_to'][0]['evolution_details'][0]['min_level'];
          $stone = $rEvo['chain']['evolves_to'][0]['evolution_details'][0]['item']['name'];
          $happy = $rEvo['chain']['evolves_to'][0]['evolution_details'][0]['min_happiness'];
          
          if($stone != ""){
            echo (" with a ". $stone. ".<br>");
          }
          
          if($happy != null){
            echo (" with a minimal happiness of " . $happy);
          }
          
          if( $level != ""){
            echo (" at level " . $level . "<br>");
          }
          else{echo("<br>");}
        }
        if($rEvo['chain']['evolves_to'][0]['evolves_to'][0]['species'] != ""){
          echo ("<a href='result.php?nameFromList=".$name2 . "'>".$name2. "</a> evolves into ");
          echo ("<a href='result.php?nameFromList=".$name3 . "'>".$name3. "</a>");
          
          $level2 = $rEvo['chain']['evolves_to'][0]['evolves_to'][0]['evolution_details'][0]['min_level'];
          $happy2 = $rEvo['chain']['evolves_to'][0]['evolves_to'][0]['evolution_details'][0]['min_happiness'];
          $stone2 = $rEvo['chain']['evolves_to'][0]['evolves_to'][0]['evolution_details'][0]['item']['name'];
          if($happy2 != null){
            echo (" with a minimal happiness of " . $happy2);
          }
          
          if($stone2 != ""){
            echo (" with a ". $stone2. ".<br>");
          }
          if( $level2 != ""){
            echo(" at level ". $level2 . "<br>");
          }
          else{echo("<br>");}
        }
      }else{
        echo ("Request for evolution chain came back empty, try refreshing.");
      }
    //</evolution>
      
    }else{
      echo("Request for description came back empty, try refreshing.");
    }
    echo ("<br /><br />");
    //Method one to get types; Works
    echo ("Its types are : <br /><table>");
    $count = 0;
    while($count != count($rData['types'])){
      echo ("<tr><th>Type : &nbsp;</th><td> " . $rData['types'][$count]['type']['name'] . "</td></tr>");
      $count++;
    }
  
     echo ("</table><br />");
    //Getting base stats
    echo("Its base stats are : <br /><table>");
    
    echo ("<tr><th>Attack : &nbsp;</th><td> ".$rData['stats'][4]['base_stat']. "</td></tr>");
    echo ("<tr><th>Defence : &nbsp;</th><td> ".$rData['stats'][3]['base_stat']. "</td></tr>");
    echo ("<tr><th>Special-Attack : &nbsp;</th><td> ".$rData['stats'][2]['base_stat']. "</td></tr>");
    echo ("<tr><th>Special-Defence : &nbsp;</th><td> ".$rData['stats'][1]['base_stat']. "</td></tr>");
    echo ("<tr><th>Speed : &nbsp;</th><td> ".$rData['stats'][0]['base_stat']. "</td></tr>");  
    echo ("<tr><th>HP : &nbsp;</th><td> ".$rData['stats'][5]['base_stat']. "</td></tr></table>");
    echo ("<br /><br />");
    echo("Its Abilities it can have : <br>");
    $abCount = 0;
    while($abCount != count($rData['abilities'])){
      echo("- ".$rData['abilities'][$abCount]['ability']['name'] . "<br>");
      $abCount++;
    }
    echo ("<br>");
    $encFile = "pokemon/enc". $name . ".txt";
      if(file_exists($encFile) && (time() - $cachetime < filemtime($encFile))){
        $encouterData = file_get_contents($encFile);
      }else{
        $encounterData = file_get_contents("http://pokeapi.co". $rData['location_area_encounters']);
        if($encounterData != ""){
          file_put_contents($encFile, $encounterData);
        }
      }
    $rEncounter = json_decode($encounterData, true);
    
    
    echo("<table class='sortable' id='movesTable'><tr><th>Move</th><th>Teached</th></tr>");
    $moveCount = 0;
    while($moveCount != count($rData['moves'])){
      $move = $rData['moves'][$moveCount]['move']['name'];
      $moveLevel = $rData['moves'][$moveCount]['version_group_details'][0]['level_learned_at'];
      $buildMove = "Learned at level &nbsp;";
      if($moveLevel == "0"){
        $moveLevel = $rData['moves'][$moveCount]['version_group_details'][0]['move_learn_method']['name'];
        $buildMove = "Learned with &nbsp;";
      }
      if($moveLevel > 0 && $moveLevel < 10){
        $moveLevel = "0".$moveLevel;
      }
      echo("<tr><td>".$move."&nbsp;&nbsp;</td><td>" .$buildMove.$moveLevel."</td></tr>");
      $moveCount++;
    }
    echo("</table><br><br>");
  }else{
    echo ("No pokemon found with that name: ".$name);
  }
?>