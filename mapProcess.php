<?php
  $inside="none";
  $ignore=false;
  $roads="";
  $woods="";
  
	function startElement($parser, $entityname, $attributes) {
      global $inside;
      global $ignore;

      // Layers
      global $woods;
      global $roads;

      global $roads;    // Global for road layer
      global $woods;    // Global for woods layer

      if($entityname=="DEFS"){
          $inside=$entityname;
      }

      // Ignore all elements inside "defs"
      if($inside!="DEFS"){
          // echo $entityname."\n";

    			if($entityname=="SVG"){
              echo "<svg ";				
    					foreach ($attributes as $attname => $attvalue) {
    							echo $attname."='".$attvalue."' ";
    					}
              echo ">";
    			}else if($entityname=="PATH"){
              $styles=$attributes['STYLE'];
              if(strpos($styles,"rgb(100%,100%,100%)")===false){
                  // We print most paths as given
                  $path="<path ";
        					foreach ($attributes as $attname => $attvalue) {
                      if($attname=="STYLE"){
                          $kind="land";

                          if(strpos($attvalue,"rgb(90.196078%,43.137255%,53.72549%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(96.470588%,58.823529%,47.843137%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(95.686275%,76.470588%,49.019608%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(47.058824%,47.058824%,47.058824%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(73.333333%,73.333333%,73.333333%)")!==false) $kind="road";
                          if(strpos($attvalue,"stroke:rgb(66.666667%,82.745098%,87.45098%)")!==false) $kind="River";
                          if(strpos($attvalue,"stroke:rgb(40%,40%,100%)")!==false) $kind="Ferryline";
                          //if(strpos($attvalue,"rgb(66.666667%,82.745098%,87.45098%)")!==false) $kind="Water";
                          
                          

                          if(strpos($attvalue,"rgb(81.568627%,81.568627%,81.568627%)")!==false) $kind="town";
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
                          
                          

                         
                     
                          

                          

                          

                          

                          



                          

                          
                          


                          


                          

                          

                          

/*
                          if(strpos($attvalue,"rgb(93.333333%,94.117647%,83.529412%)")!==false) $kind="fields";
                          if(strpos($attvalue,"rgb(100%,94.509804%,72.941176%)")!==false) $kind="beach";
                          if(strpos($attvalue,"rgb(83.921569%,85.098039%,62.352941%)")!==false) $kind="beach";
                          if(strpos($attvalue,"rgb(87.058824%,96.470588%,75.294118%)")!==false) $kind="park";
                          if(strpos($attvalue,"rgb(80.392157%,92.156863%,69.019608%)")!==false) $kind="park";
                          if(strpos($attvalue,"rgb(67.843137%,81.960784%,61.960784%)")!==false) $kind="woods";
*/                          
                      }
                      if($attname=="D"){
                          // We process draw commands to remove all decimals except for last two decimals
          							  $path.=$attname."='";
                          $commands=explode(" ",$attvalue);
                          foreach($commands as $command){
                              if(strpos($command,".")!==false){
                                  $pos=strpos($command,".");
                                  $command=substr($command,0,$pos+2);
                              }
                              $path.=$command." ";
                          }
                          $path.="'";
                      }else{
          							  $path.=$attname."='".$attvalue."' ";                      
                      }
        					}
                  $path.="></path>";
                  if($kind=="land") echo $path;
              }else{
                  $ignore=true;
                  //echo "<path>";
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
              echo $svg;
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
   // echo "<br>Parsing Completed in ".$chunks." chunks</br>";
 
   xml_parser_free($parser);
?>