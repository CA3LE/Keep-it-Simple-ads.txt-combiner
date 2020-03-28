<?

# Keep it Simple ads.txt combiner by Damon @ TestMy.net 2020

# This script will check freestar's ads.txt, compare the date to your local ads.txt and update only if needed.
# It will also combine your site's default ads.txt with freestar's.  
# IMPORTANT: Make sure your server ALWAYS has disk space for the write... otherwise you may end up with a zero byte ads.txt file, obviously not ideal.

$pathToRoot = './'; // should have ads.txt and ads-publisher.txt | include trailing slash | default: same directory as script ./ | e.g. (if script is two directories from root) ../../
$sourceAdsTxt = "https://a.pub.network/core/ads.txt"; // freestar's frequently updated ads.txt
// ads-publisher.txt $sourceAdsTxt get combined into the final output

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

if (preg_match($pattern, $saveVar, $dateRemote) && preg_match($pattern, $adsTXTfc, $dateLocal)) { // find the dates in strings
   echo "Found dates | remote: {$dateRemote[0]} & local: {$dateLocal[0]} ";
}else{
	echo "dates not found";
}

if($dateRemote[0] <= $dateLocal[0] && $dateLocal[0]){ // compare dates to continue // also continue if !$dateLocal[0]
	echo " -- Date match, I'm done for now --  ";
	echo $pathToRoot."ads.txt is already up-to-date.";
}else{
	echo " -- Date mis-match, Let's update...  ";
	if (strpos($saveVar, '# PUBLISHER SPECIFIC ADS.TXT INFO BELOW THIS LINE') !== false) { // only continue if complete $sourceAdsTxt is loaded in $saveVar
		$adsTXT = fopen($pathToRoot.'ads.txt','w'); // open local ads.txt for writing
		$adsPublisherTXT = file_get_contents($pathToRoot.'ads-publisher.txt', true); // put local ads-publisher.txt into variable
		$saveVar .= $adsPublisherTXT; // combine $adsPublisherTXT and $saveVar
		fwrite($adsTXT,$saveVar); // write ads.txt file
		fclose($adsTXT); // close file
		echo $pathToRoot."ads.txt updated  <p>output:</p>";
		echo "<pre>$saveVar</pre>"; // show client what we saved
	}else{
		echo"error / not saved";
	}
}

// Make cronjob to execute every 10 minutes.  


?>