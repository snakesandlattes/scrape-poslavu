<?php

/* 
  Run this script:
    $ php lavu.php
*/

function post_to_api($postvars)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://admin.poslavu.com/cp/reqserv/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}

function responseToJSON($response)
{
	$dom = new DOMDocument;
	$dom->loadXML($response);

	$doc = $dom->documentElement;
	$first = 0;

	$result = "";
	foreach($doc->childNodes as $row)
	{
		if($first % 2 == 1)
		{
			if($result != "")
				$result .= ",\n";
			$inner = "";
			foreach($row->childNodes as $entry)
				if($entry->nodeName != "#text")
				{
					if($inner != "")
						$inner .= ", ";
						
					$inner .= '"' . $entry->nodeName . '" : "' . $entry->nodeValue . '"';
				}

			$result .= "\t{\n\t\t" . $inner . "\n\t}";
		}
		$first++;
	}

	$result = "[\n" . $result . "\n]\n";

	return $result;
}

$r = post_to_api(
    array(
        'valid_xml' => '1',
        // jsyang: get the credentials from the API page and fill them in here.
        'dataname'  => '__DATANAME__',
        'token'     => '__TOKEN_____',
        'key'       => '__KEY_______',
        'table'     => 'orders',
        
        // How many entries to grab at a time.
        'limit'     => '0,2000'
    )
);

echo $r;

?>
