<?php

//Given a start and stop file, the location of the files and their pre-fix, 
//this function puts file names that fit the start-stop parameter and stores
//the results in an array
function carve($start, $stop, $dir, $pre){

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
			//assign start and end timestamp
			$start_tstamp = $start_token[1];
			$stop_tstamp  = $stop_token[1];
			print "Start Tstamp : ".$start_tstamp;
			print "\nStop Tstamp  : ".$stop_tstamp."\n";
			//store capture directory contents into variable
			$dircontents = list_dir($dir,$pre);
			echo gettype($dircontents);
			echo "\n";
			//extract since it was passed from list_dir function
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

//takes directory and file prefix and adds all files in the directory
//with the given prefix to an array ($valid_files)
function list_dir($directory,$pre){

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
	return $valid_files;
}

//Takes sorted list of files, the directory they are located in and the prefix of the 
//file name as arguments and retrieves each file's size and stores it in an array
function get_sizes($files_array,$dir,$pre){

	for($i=0;$i<count($files_array);$i++){
		$postfix = $files_array[$i];
		$size =filesize("$dir"."$pre"."."."$postfix");

		$size_array[$i] = $size;

	}
	return $size_array;

}

//Takes carved out files and their sizes as arguments along with user supplied arguments
//to make non-blocking concurrent calls to cxt2pcap.pl and produces an out file for each
//call and stores it in a folder in the /tmp/ directory
//*TODO*: make directory to write results to
//		  merge output files into one file using mergecap
function cxt2pcap($files2search,$file_sizes,$options){

	$dirname = '/tmp/multicarve_results/';

	if (!file_exists($dirname)) {
	    mkdir($dirname, 0655);
	    echo "The directory $dirname was successfully created.\n";
	    exit;
	} 
	else {
	    echo "The directory $dirname exists.\n";
	}
	//need to make a directory in tmp and write output to that directory
	$j=1;
	for($i=0;$i<count($files2search);$i++){
		if($i==0){
			//nohup means to ignore hangup signal 
			//setsid creates a new session if the calling process is not a process group leader 
			//dont have to include 'perl' in the command if the cxt2pcap is in the user's PATH (suggested /usr/local/bin)
			exec('nohup setsid perl cx2pcap.pl -r '.$files2search[$i].
										  ' -w out'.[$j].'.pcap'.			//writes files to out1.pcap out2.pcap out3.pcap etc...
										' --proto '.$options["proto"].
									   ' --src-ip '.$options["src-ip"].
									 ' --src-port '.$options["src-port"].
								       ' --dst-ip '.$options["dest-ip"].
								     ' --dst-port '.$options["dest-port"].
								     	     ' -s '.$options["s"].
											 ' -e '.$file_sizes[$i].
                ' > /dev/null 2>&1 &');										//this redirects stdio and stderr to dev/null
			$j++; 															//increment out file postfix
			print "im in the loop";
		}
		else{
			//nohup means to ignore hangup signal 
			//setsid creates a new session if the calling process is not a process group leader 
			//dont have to include 'perl' in the command if the cxt2pcap is in the user's PATH (suggested /usr/local/bin)
			exec('nohup setsid perl cx2pcap.pl -r '.$files2search[$i].
										  ' -w out'.[$j].'.pcap'.			//writes files to out1.pcap out2.pcap out3.pcap etc...
										' --proto '.$options["proto"].
									   ' --src-ip '.$options["src-ip"].
									 ' --src-port '.$options["src-port"].
								       ' --dst-ip '.$options["dest-ip"].
								     ' --dst-port '.$options["dest-port"].
								     	     ' -s '.$options["s"].
											 ' -e '.$file_sizes[$i].
                ' > /dev/null 2>&1 &');										//this redirects stdio and stderr to dev/null
			$j++; 
		}
	echo "im here!2\n";
	}
}


//define command line arguments
$shortopts  = "";
$shortopts .= "w";  	//file to write results to 				//may not need this but keeping it here for now
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

//get files to search
$files2search = carve($start,$stop,$dir,$pre);
//get sizes of the files you want to search
$file_sizes = get_sizes($files2search,$dir,$pre);
//construct and call cxt2pcap searches
cxt2pcap($files2search,$file_sizes,$options);

//debug print
print_r($files2search);
print_r($file_sizes);


