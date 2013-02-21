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
		if (count($start_token)!=2 or count($stop_token)!=2 ) {
			print "Invalid file name format, must be two strings separated by a period (.)\n";
		}
		else{	
			print "Start Token : ".print_r($start_token)."\n";
			print "Start Token Length: ".count($start_token)."\n";
			print "Stop Token : ".print_r($stop_token)."\n";
			print "Stop Token Length: ".count($stop_token)."\n";

			$start_tstamp = $start_token[1];
			$stop_tstamp  = $stop_token[1];
			print "Start Timestamp: ".$start_tstamp."\nStop Timestamp: ".$stop_tstamp."\n";

		}
		
	}
	//else: Files are the same
	else{
		print "Files are the same, use cx2pcap.pl dumbass!\n";
	}
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
//sanitize user input using escapeshellarg()
//$start = escapeshellarg($options["s"]);
//$stop  = escapeshellarg($options["e"]);
$start = $options["s"];
$stop  = $options["e"];
//
carve($start,$stop);

?>
