<?php

# Keep it Simple ads.txt combiner by Damon @ TestMy.net 2020

# This script will check freestar's ads.txt, compare the date to your local ads.txt and update only if needed.
# It will also combine your site's custom ads.txt lines with freestar's master ads.txt.
# IMPORTANT: Make sure your server ALWAYS has disk space for the write... otherwise you may end up with a zero byte ads.txt file, obviously not ideal.

$pathToRoot = './'; // should have ads.txt and ads-publisher.txt | include trailing slash | default: same directory as script ./ | e.g. (if script is two directories from root) ../../
$sourceAdsTxt = "https://a.pub.network/core/ads.txt"; // freestar's frequently updated ads.txt
# ads-publisher.txt $sourceAdsTxt get combined into the final output

# additional options
//$stripComments = true; // remove commented lines
//$stripBlanklines = true;  // remove blank lines
//$publisherFirst = true; // put publisher's lines before freestar
//$siteName = 'Freestar.io'; // brand the file
//$noBrand = true; // remove brand

function curlFetch($src){
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $src); // Set the url
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true); // Set the result output to be a string.
	$output = curl_exec($handle);
	curl_close($handle);
	return $output;
}

$saveVar = curlFetch($sourceAdsTxt); // get ads.txt from freestar and put information into variable
$pattern = "/\d{2}\-\d{2}\-\d{2}/"; // pattern for date search | formatted e.g. 03-28-20
$adsTXTfc = file_get_contents($pathToRoot.'ads.txt', true); // put current local ads.txt into variable

if(!$siteName){
	$siteName = "Freestar.io";
}
if (preg_match($pattern, $saveVar, $dateRemote) && preg_match($pattern, $adsTXTfc, $dateLocal)) { // find the dates in strings
	echo "Dates found | remote: {$dateRemote[0]} & local: {$dateLocal[0]}";
}else{
	echo "Dates not found";
}
if($dateRemote[0] == $dateLocal[0]){ // compare dates to continue
	echo " -- Date match, I'm done for now --  ";
	echo $pathToRoot."ads.txt is already up-to-date.";
}else{
	echo " -- Date mis-match, Let's update --  ";
	if (strpos($saveVar, '# PUBLISHER SPECIFIC ADS.TXT INFO BELOW THIS LINE') !== false) { // only continue if complete $sourceAdsTxt is loaded in $saveVar
		$adsTXT = fopen($pathToRoot.'ads.txt','w'); // open local ads.txt for writing
		$adsPublisherTXT = file_get_contents($pathToRoot.'ads-publisher.txt', true); // put local ads-publisher.txt into variable
		if($publisherFirst){
			$saveVar = $adsPublisherTXT.PHP_EOL.$saveVar; // combine $adsPublisherTXT and $saveVar
		}else{
			$saveVar .= $adsPublisherTXT; // combine $adsPublisherTXT and $saveVar
		}
		if($stripComments){
			$array = explode("\n",$saveVar); // array all lines
			foreach($array as $arr) {
			    if(!preg_match('/^#/',$arr)) { // if line does not start with # add line to $output
			        $output[] = $arr;
			    }
			}
			$saveVar = implode("\n",$output); // put array together back into $saveVar
			$saveVar = "# Last updated {$dateRemote[0]}\n".PHP_EOL.$saveVar; // append "# Last updated" line
			if(!$noBrand){
				$saveVar = "# $siteName ads.txt file".PHP_EOL.$saveVar;
			}
		}
		if($stripBlanklines){
			$saveVar = str_replace( "\n\n" , "\n" ,$saveVar); // change double breaks into single
		}
		fwrite($adsTXT,$saveVar); // write ads.txt file
		fclose($adsTXT); // close file
		echo $pathToRoot."ads.txt updated  <p>output:</p>";
		echo "<pre>$saveVar</pre>"; // show client what we saved
	}else{
		echo"error / not saved";
	}
}

# For testing change the date in your ads.txt to make the program mis-match and force an update.
# After manual testing is sucessful, make cronjob to execute this every 10 minutes.

?>
