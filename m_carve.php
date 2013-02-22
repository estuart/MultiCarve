<?php

//performs carve accross multiple files
function carve($start, $stop)
{    
	//if true: files are different
	if(strcmp($start,$stop)){
		print "Start File : ".$start."\n";
		print "Stop  File : ".$stop."\n";
		//tokenize start and end file name by (.)
		$start_token = explode(".",$start);
		$stop_token  = explode(".",$stop);
		//check to make sure file is in partA.partB format (size 2)
		if(count($start_token)!=2 or count($stop_token)!=2){
			print "Invalid file name format, must be two strings separated by a period (.)\n";
		}
		else{
			//assign start and end timestamp to variables
			$start_tstamp = $start_token[1];
			$stop_tstamp  = $stop_token[1];
			
			//store capture directory contents into variable
			//have to extract since it was passed from list_dir function
			$dircontents = list_dir("../test_data/");
			extract($dircontents);
			
			//if first x digits match (in this case 6) then treat them as matches
			for($i=0; $i<(count($dircontents)); $i++){
				$temp = $dircontents[$i];										//may not need to do this since i fixed the if statemtn below
				print "temp: ".$temp;
				if(substr_compare($start_tstamp,$temp,0,5)==0){					//currently not working correctly
					print "\nmatch: ".$start_tstamp."\n".$dircontents[$i]."\n\n";
				}
				 else{
				 	print "other";
				 }
			}
		}
	}
	//else: Files are the same
	else{
		print "Files are the same, use cxt2pcap.pl dumbass!\n";
	}
}

function list_dir($directory)
{
	$open_directory = opendir($directory);
		while($filename = readdir($open_directory)){
			$filesplit = explode(".", $filename);
			$check_filename = $filesplit[0];
				if($check_filename=="cxt"){
					$valid_files[] = $filesplit[1];
				}

		}
	closedir($directory);
	sort($valid_files);
	return $valid_files;



}

//define command line arguments
$shortopts  = "";
$shortopts .= "s:"; //starting file
$shortopts .= "e:"; //ending file
$options = getopt($shortopts);

//if insufficient number of arguments, throw error
if (count($options)!=2){
	print "[Error]: Insufficient number of args or incorrect flag\n\n";
	exit(1);
}

//map options to variables
$start = $options["s"];
$stop  = $options["e"];
//
carve($start,$stop);

?>
