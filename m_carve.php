<?php
//carves out files that fit given start and stop range and stores name and size in $file_data ob
function carve($start, $stop, $dir, $pre)
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
			print "Invalid file name format, must be two strings separated by a period (eg. cxt.12345)\n";
		}
		else{
			//assign start and end timestamp to variables
			$start_tstamp = $start_token[1];
			$stop_tstamp  = $stop_token[1];
			print "Start Tstamp : ".$start_tstamp;
			print "\nStop Tstamp  : ".$stop_tstamp."\n";
			//store capture directory contents into variable
			//have to extract since it was passed from list_dir function
			//$dircontents = list_dir("~/scripts/data");
			//$dir = '../scripts/data/';
			$dircontents = list_dir($dir,$pre);
			echo gettype($dircontents);
			echo "\n";
			extract($dircontents);
			//if first x digits match (in this case 6) then treat them as matches
			//and add to the results array
			$carve_results = array();
			$j=$i=0;
			for($i; $i<(count($dircontents)); $i++){

				if(substr_compare($start_tstamp,$dircontents[$i],0,5)==0){
					if($dircontents[$i]>=$start_tstamp and $dircontents[$i]<=$stop_tstamp){
						$carve_results[$j]=$dircontents[$i];
						$j++;
					}	
					
				}
			}
		}
	}
	else{
		print "Files are the same, use cxt2pcap.pl dumbass!\n";
	}
	
	//sort results and return array
	print "Searched ".count($dircontents)." files and found ".count($carve_results)." matching search criteria:\n";
	sort($carve_results);
	return $carve_results;
}

function list_dir($directory,$pre)
{	
	$directory = $directory;
	$open_directory = opendir($directory);
	while($filename = readdir($open_directory)){

		$filesplit = explode(".", $filename);
		$check_prefix = $filesplit[0];
		if($check_prefix==$pre){						
			$valid_files[] = $filesplit[1];
		}
	}
	closedir();
	//return valid files to be searched through
	return $valid_files;
}

function get_sizes($files_array){
	//takes sorted list of files to be searched as argument and places
	//their file size in an array with corresponding indices 
	//ex. $files_array[0]= file 0 ; $size_array[0] = size of file 0
	for($i=0;$i<count($files_array);$i++){
		//need to make directory and prefix a variable
		$size =filesize("../scripts/data/cxt.".$files_array[$i]);
		$size_array[$i] = $size;

	}
	return $size_array;

}

//function cxt2pcap($files2search,$file_sizes,$write2, $proto, $srcip, $srcprt, $destprt, $start_offset, $end_offset )
function cxt2pcap($files2search){

	$j=0;
	for($i=0;$i<count($files2search);$i++){
		//if($i==0){
			exec('nohup setsid gedit test'.$j.'.txt > /dev/null 2>&1 &');
			$j++;
		//}
	}
}


//define command line arguments
$shortopts  = "";
$shortopts .= "w";  	//file to write results to
$shortopts .= "s"; 		//Byteoffset on where to start carving
$shortopts .= "e"; 		//Byteoffset on where to end carving
$longopts  = array(
    "sfile:",     		//file to start search at, required
    "efile:",    		//file to end search at, required
    "proto",        	//protocol
    "src-ip",			//source IP
    "src-port",			//source port
    "dest-ip",			//destination ip
    "dest-port",		//destination port
    "dir:",				//path to directory of pcap files
    "pre:"				//prefix of files you are searching
    						//ex. --pre cxt is valid for a file like cxt.123456789

);
$options = getopt($shortopts, $longopts);


//map options to variables
$start = $options["sfile"];
$stop  = $options["efile"];
$dir   = $options["dir"];
$pre   = $options["pre"];
$write2 = $options["w"];
$proto = $options["proto"];
$srcip = $options["src-ip"];
$srcprt = $options["src-port"];
$destprt = $options["dest-port"];
$start_offset = $options["s"];
$end_offset = $options["e"];

$a =getcwd();
print $a;
$string = "'" .$dir . "'";
print $string;
print "\n";
$files2search = carve($start,$stop,$dir,$pre);
$file_sizes = get_sizes($files2search);
cxt2pcap($files2search,$file_sizes,$write2, $proto, $srcip, $srcprt, $destprt, $start_offset, $end_offset);

print_r($files2search);
print_r($file_sizes);

//$file_sizes = get_sizes($file_range)

//now call cxt2pcap.pl with user inputted parameters plus filerange and filesizes