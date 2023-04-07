<?php
  header('Content-Type: image/svg+xml');

  $inside="none";
  $ignore=false;
  $roads="";
  $woods="";

  $kinds=Array();

  $work=null;

  // Area Skipping
  $areaskip=false;

  // Tag Handling
  $paths=Array();
  $stylecoll=Array();
  $svgtag="";

  function removecommas($val)
  {

      if(strpos($val,".")!==false){
          $pos=strpos($val,".");
          return substr($val,0,$pos+2);
      }else{
          return $val;
      }
  }

  function processCommands($work)
  {
       global $areaskip;

       $cnt=count($work);
       $i=0;
       $cx=0;
       $cy=0;
       
       $treshold=80;
       $widthtreshold=10;

       $str="";

       $minx=10000;
       $maxx=-10000;
       $miny=10000;
       $maxy=-10000;

       if($cnt>0){
           // Squared minimum distance
           // We read minimum x,y and maximum x,y
           while($i<$cnt){
              $command=$work[$i];
              if($command=="Z"){
                  $str.="Z ";
                  $i+=1;
              }else if($command=="L"){
                  $dx=$cx-$work[$i+1];
                  $dy=$cy-$work[$i+2];
                  if(($i+3)<$cnt){
                      $next=$work[$i+3];
                  }else{
                      $next="UNK";
                  }
                  if(((($dx*$dx)+($dy*$dy))>$treshold)||$next!="L"){
                      $str.=$command.removecommas($work[$i+1]).",".removecommas($work[$i+2]);
                      $cx=$work[$i+1];
                      $cy=$work[$i+2];
                  }else{
                  
                  }

                  if($cx<$minx) $minx=$cx;
                  if($cx>$maxx) $maxx=$cx;
                  if($cy<$miny) $miny=$cy;
                  if($cy>$maxy) $maxy=$cy;

                  $i+=3;
              }else if($command=="M"){
                  $str.=$command.removecommas($work[$i+1]).",".removecommas($work[$i+2]);

                  // Assign cx and cy due to start of curve
                  $cx=$work[$i+1];
                  $cy=$work[$i+2];

                  if($cx<$minx) $minx=$cx;
                  if($cx>$maxx) $maxx=$cx;
                  if($cy<$miny) $miny=$cy;
                  if($cy>$maxy) $maxy=$cy;

                  $i+=3;      
              }else{
                  // echo "Other:".$work[$i]."</td>";
                  $i++;
              }

           }
      }

      // Remove commands if less than width treshold
      if((($maxx-$minx)<$widthtreshold)||(($maxy-$miny)<$widthtreshold)){
          $areaskip=true;
      }else{
          $areaskip=false;
      }

      return $str;        
  }
  
	function startElement($parser, $entityname, $attributes) {
      global $inside;
      global $ignore;

      global $work;

      // Layers
      global $woods;
      global $roads;

      global $roads;    // Global for road layer
      global $woods;    // Global for woods layer
      global $kinds;    // Global array for kinds

      global $areaskip; // Global for skipping areas

      global $paths;
      global $svgtag;

      global $stylecoll;

      if($entityname=="DEFS"){
          $inside=$entityname;
      }

      // Ignore all elements inside "defs"
      if($inside!="DEFS"){
          // echo $entityname."\n";

    			if($entityname=="SVG"){
              $svgtag="<svg ";				
    					foreach ($attributes as $attname => $attvalue) {
    							$svgtag.=strtolower($attname)."='".$attvalue."' ";
    					}
              $svgtag.=">";
    			}else if($entityname=="PATH"){
              $styles=$attributes['STYLE'];

              $stylelist=explode(";",$styles);

              $styles="";
              foreach($stylelist as $lstyle){
                  $instyle=explode(":",$lstyle);
                  if($instyle[0]=="fill"||$instyle[0]=="stroke-width"||$instyle[0]=="stroke"){
                      $styles.=$instyle[0].":".$instyle[1].";";
                  }
              }

              if(strpos($styles,"rgb(100%,100%,100%)")===false){
                  // We print most paths as given
                  $path="<path ";
                  $kind="";
        					foreach ($attributes as $attname => $attvalue) {
                      if($attname=="STYLE"){
                          $kind="none";

                          if(strpos($attvalue,"rgb(90.196078%,43.137255%,53.72549%)")!==false) $kind="Road";
                          if(strpos($attvalue,"rgb(96.470588%,58.823529%,47.843137%)")!==false) $kind="Road";
                          if(strpos($attvalue,"rgb(95.686275%,76.470588%,49.019608%)")!==false) $kind="Road";
                          if(strpos($attvalue,"rgb(47.058824%,47.058824%,47.058824%)")!==false) $kind="Road";
                          if(strpos($attvalue,"rgb(73.333333%,73.333333%,73.333333%)")!==false) $kind="Road";
                          if(strpos($attvalue,"stroke:rgb(66.666667%,82.745098%,87.45098%)")!==false) $kind="River";
                          if(strpos($attvalue,"stroke:rgb(40%,40%,100%)")!==false) $kind="Ferryline";
                          if(strpos($attvalue,"rgb(66.666667%,82.745098%,87.45098%)")!==false) $kind="Water";

                          if(strpos($attvalue,"rgb(81.568627%,81.568627%,81.568627%)")!==false) $kind="Town";
                          if(strpos($attvalue,"rgb(91.372549%,90.588235%,88.627451%)")!==false) $kind="Airport";
                          if(strpos($attvalue,"rgb(73.333333%,73.333333%,80%)")!==false) $kind="Runway";
                          if(strpos($attvalue,"rgb(51.764706%,38.039216%,76.862745%)")!==false) $kind="Airport Icon";
                          if(strpos($attvalue,"rgb(78.039216%,78.039216%,70.588235%)")!==false) $kind="Construction Area";

                          if(strpos($attvalue,"rgb(100%,33.333333%,33.333333%)")!==false) $kind="Military";
                          if(strpos($attvalue,"rgb(77.254902%,76.470588%,76.470588%)")!==false) $kind="Quarry";
                          if(strpos($attvalue,"rgb(92.156863%,85.882353%,90.980392%)")!==false) $kind="Industrial";                         

                          if(strpos($attvalue,"rgb(78.431373%,98.039216%,80%)")!==false) $kind="Park";
                          if(strpos($attvalue,"rgb(67.843137%,81.960784%,61.960784%)")!==false) $kind="Forest";
                          if(strpos($attvalue,"rgb(83.921569%,85.098039%,62.352941%)")!==false) $kind="Heath";
                          if(strpos($attvalue,"rgb(87.058824%,96.470588%,75.294118%)")!==false) $kind="Golf Course";
                          if(strpos($attvalue,"rgb(80.392157%,92.156863%,69.019608%)")!==false) $kind="Meadow";
                          if(strpos($attvalue,"rgb(87.45098%,98.823529%,88.627451%)")!==false) $kind="Sports Centre";
                          if(strpos($attvalue,"rgb(100%,94.509804%,72.941176%)")!==false) $kind="Beach";
                          if(strpos($attvalue,"rgb(93.333333%,94.117647%,83.529412%)")!==false) $kind="Farmland";
                          if(strpos($attvalue,"rgb(71.372549%,70.980392%,57.254902%);")!==false) $kind="Landfill";
                          if(strpos($attvalue,"rgb(66.666667%,79.607843%,68.627451%)")!==false) $kind="Graveyard";
                          if(strpos($attvalue,"rgb(96.078431%,91.372549%,77.647059%)")!==false) $kind="Sand";
                          if(strpos($attvalue,"rgb(66.666667%,87.843137%,79.607843%)")!==false) $kind="Sports Pitch";
                          if(strpos($attvalue,"rgb(96.078431%,86.27451%,72.941176%)")!==false) $kind="Farmyard";
                          if(strpos($attvalue,"rgb(78.823529%,88.235294%,74.901961%)")!==false) $kind="Allotment";
                          if(strpos($attvalue,"rgb(78.431373%,84.313725%,67.058824%)")!==false) $kind="Scrub";
                          if(strpos($attvalue,"rgb(68.235294%,87.45098%,63.921569%)")!==false) $kind="Orchard";
                          if(strpos($attvalue,"rgb(79.607843%,69.411765%,60.392157%)")!==false) $kind="Orchard";

                          if(strpos($attvalue,"rgb(81.568627%,56.078431%,33.333333%)")!==false) $kind="Peak";
                          if(strpos($attvalue,"rgb(83.137255%,0%,0%)")!==false) $kind="Peak";
                          
          							  //$path.=strtolower($attname)."='".$styles."' ";
                          $stylecoll[$kind]=$styles;

                      }else if($attname=="D"){
                          // We process draw commands to remove all decimals except for last two decimals
          							  $path.=strtolower($attname)."='";
                          $commands=explode(" ",$attvalue);

                          // Count drawing commands
                          if(isset($kinds[$kind])){
                              array_push($kinds[$kind],count($commands));
                          }else{
                              $kinds[$kind]=Array();                          
                              array_push($kinds[$kind],count($commands));
                          }

                          // Process commands and remove L commands inside treshold
                          $currentcommand=processCommands($commands);
                          $path.=$currentcommand;
                          $path.="'";

                      }else{
          							  $path.=strtolower($attname)."='".$attvalue."' ";                      
                      }
        					}
                  $path.="></path>\n";                  
                  
                  //if($currentcommand!=""&&($kind=="Road"||$kind=="Forest"||$kind=="Park")) echo $path;
                  if(isset($paths[$kind])){
                  }else{
                      $paths[$kind]=Array();
                  }

                  if($kind=="Forest" && !$areaskip) array_push($paths[$kind],$path);
                  if($kind=="Sand"||$kind=="Park"||$kind=="Farmland"||$kind=="Water") array_push($paths[$kind],$path);

              }else{
                  $ignore=true;
              } 
    			}else{
    			}
      }
	}
  
	function endElement($parser, $entityname) {
      global $inside;
      global $ignore;

      if($inside!="DEFS"){
    			if($entityname=="g"){
    			}else if($entityname=="SVG"){
    			}else if($entityname=="PATH"){
              if(!$ignore){
//                  echo "</path>\n";              
              }else{
                  $ignore=false;
              }
    			}else{
    			}
      }

      if($entityname=="DEFS"){
          $inside="none";
      }
	}
  
   function charData($parser, $chardata) {
   		$chardata=trim($chardata);
   		if($chardata=="") return;
		 	echo $chardata;
   }
  
  $parser = xml_parser_create();
  $indent = 0;
  xml_set_element_handler($parser, "startElement", "endElement");
  xml_set_character_data_handler($parser, "charData");
 
  $file = 'map.svg';
  if (!($handle = fopen($file, "r"))) {
      die("could not open XML input");
  }
  $chunkSize=8192;
  $chunks=0;
  while(!feof($handle)) 
  {   
      $data = fread($handle, $chunkSize);
      if (!xml_parse($parser, $data)) {         
          printf("<P> Error %s at line %d</P>", xml_error_string(xml_get_error_code($parser)),xml_get_current_line_number($parser));
      } 
      $chunks++;
   }

   // Output SVG
   echo $svgtag;

   foreach($paths as $kind=>$value){
      echo "<g id='".$kind."' ";
      if(isset($stylecoll[$kind])){
          echo "style='".$stylecoll[$kind]."' ";
      }
      echo ">\n";
      if($kind=="Forest"||$kind=="Sand"||$kind=="Park"||$kind=="Farmland"||$kind=="Water"){
          foreach($value as $path){
              echo $path;
          }
      }
      echo "</g>\n";
   }

   echo "</svg>";
 
   xml_parser_free($parser);
?>