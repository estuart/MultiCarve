<?php

//carves out files that fit given start and stop range
function carve($start, $stop)
{ 
	//if true: files are different
	if(strcmp($start,$stop)){
		print "Start File : ".$start."\n";
		print "Stop  File : ".$stop."\n";
		//tokenize start and end file name by (.)
		$start_token = explode(".",$start);
		$stop_token  = explode(".",$stop);
		//check to make sure file is in (prefix).(suffix) format (size 2)
		if(count($start_token)!=2 or count($stop_token)!=2){
			print "Invalid file name format, must be two strings separated by a period (.)\n";
		}
		else{
			//assign start and end timestamp to variables
			$start_tstamp = $start_token[1];
			$stop_tstamp  = $stop_token[1];
			print "Start Tstamp : ".$start_tstamp;
			print "\nStop Tstamp  : ".$stop_tstamp."\n";
			//store capture directory contents into variable
			//have to extract since it was passed from list_dir function
			$dircontents = list_dir("../scripts/data");
			extract($dircontents);
			//if first x digits match (in this case 6) then treat them as matches
			//and add to the results array
			$j=$i=0;
			for($i; $i<(count($dircontents)); $i++){
				
				if(substr_compare($start_tstamp,$dircontents[$i],0,5)==0){		
					$carve_results[$j]=$dircontents[$i];
					$j++;
				}
				else{
				 	 ;//do nothing
				}
			}
		}
	}
	else{
		print "Files are the same, use cxt2pcap.pl dumbass!\n";
	}
	
	//sort results and return array
	sort($carve_results);
	print "Searched ".count($dircontents)." files and found ".count($carve_results)." matching criteria:\n";
	for($i=0;$i<count($carve_results);$i++){
		print "cxt.".$carve_results[$i]."\n";
	}
	return $carve_results;
}

function list_dir($directory)
{
	$open_directory = opendir($directory);
	while($filename = readdir($open_directory)){
		print filesize($directory."/".$filename)."\n";
		
		$filesplit = explode(".", $filename);
		$check_filename = $filesplit[0];
		//add file endings to first row of arrray(i)
		//add corresponding file sizes to next row(j)
		#can add user defined prefix later
		if($check_filename=="cxt"){						
			$valid_files[] = $filesplit[1];
		}
	}
	//need to closedir()
	//return valid files to be searched through
	return $valid_files;
}

function get_sizes($files_array){
	//calculate file sizes of carved files


}

function cxt2pcap(){
	print "placeholder";
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
$file_range = carve($start,$stop);
//$file_sizes = get_sizes($file_range)
extract($file_range);
var_dump($file_range);
//now call cxt2pcap.pl with user inputted parameters plus filerange and filesizes

?>

