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
                          if(strpos($attvalue,"rgb(67.843137%,81.960784%,61.960784%)")!==false) $kind="woods";
                          if(strpos($attvalue,"rgb(90.196078%,43.137255%,53.72549%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(96.470588%,58.823529%,47.843137%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(95.686275%,76.470588%,49.019608%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(47.058824%,47.058824%,47.058824%)")!==false) $kind="road";
                          if(strpos($attvalue,"rgb(73.333333%,73.333333%,73.333333%)")!==false) $kind="road";

                          if(strpos($attvalue,"rgb(81.568627%,81.568627%,81.568627%)")!==false) $kind="town";

                          if(strpos($attvalue,"rgb(93.333333%,94.117647%,83.529412%)")!==false) $kind="fields";

                          

                          
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