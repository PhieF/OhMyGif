<?php
/*
 * To federate
 * create a "federated" text file in this directory
 * fill it with one instance per line. Example : 
 * https://omg.phie.fi
 * 
 * (instances must start with http)
 * create a cron job for federation, please one time a day should be enough
 * 
 * 
 * */
require_once('../config/config.php'); 
require_once('../gif_db_helper.php');
require_once('../upload_helper.php');
chdir("..");
/*gzip*/
function get_url($url)
{
    //user agent is very necessary, otherwise some websites like google.com wont give zipped content
    $opts = array(
        'http'=>array(
            'method'=>"GET",
            'header'=>"Accept-Language: en-US,en;q=0.8rn" .
                        "Accept-Encoding: gzip,deflate,sdchrn" .
                        "Accept-Charset:UTF-8,*;q=0.5rn" .
                        "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:19.0) Gecko/20100101 Firefox/19.0 FirePHP/0.4rn"
        )
    );
 
    $context = stream_context_create($opts);
    $content = tor_file_get_contents($url); 
     
    //If http response header mentions that content is gzipped, then uncompress it
    /*foreach($http_response_header as $c => $h)
    {
        if(stristr($h, 'content-encoding') and stristr($h, 'gzip'))
        {
            //Now lets uncompress the compressed data
            $content = gzinflate( substr($content,10,-8) );
        }
    }*/
    $content = gzinflate( $content );

    return $content;
}

function tor_file_get_contents($url){
	echo "get with tor";
	$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_PROXYTYPE      => CURLPROXY_SOCKS5_HOSTNAME,
    CURLOPT_PROXY          => '127.0.0.1:9050',
    CURLOPT_HEADER         => 0,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_ENCODING       => '',
    CURLOPT_COOKIEFILE     => '',
]);

$response = curl_exec($ch);

if ($response === false) {
    echo sprintf(
        "Request failed.  Error (%d) - %s\n",
        curl_errno($ch),
        curl_error($ch)
    );
    return false;
}

return $response;
}
// returns true if new Item
function saveGiphyItem($item){
	global $gif_db_helper;
	global $upload;
	echo $item->title;
         //echo $item->images->original->url;
         //giphy can have media media0 media1 so different urls. Therefore isUrlInDB isn't enough"
                echo basename(dirname($item->images->original->url))."/".basename($item->images->original->url);
                if(count($gif_db_helper->getByGifFileName(basename(dirname($item->images->original->url))."/".basename($item->images->original->url)))>0){
                        echo "already in db";
                        return false;
                }   
                $hasNew = true;
                $result = $upload->downloadGif($item->images->original->url, "");
                if($result["status"] != 0){
                        echo "error";
                        return true;
                }   
                echo $gif_db_helper->addToDb($result['url'], array(),'', $item->title,$item->title, '',$item->images->original->url, $result['webm'], $result['thumbnail']);

	return true;
}

function crawlGiphy(){
	$i=0000000000000;
	$j=0;
	$hasNew = false;
	while(true){
		$hasNew = false;
		$json = json_decode(tor_file_get_contents("https://api.giphy.com/v1/gifs/trending?api_key=3eFQvabDx69SMoOemSPiYfh9FY0nzO9x&sort=desc&offset=".($i*25)));
		foreach($json->data as $item){
			saveGiphyItem($item);

		}
		if(count($json->data) == 0){
		   if($j>20){
			$j=0;
			$i=0;
			continue;
		   }
		   echo "going random";
		   $json = json_decode(tor_file_get_contents("https://api.giphy.com/v1/gifs/random?api_key=3eFQvabDx69SMoOemSPiYfh9FY0nzO9x"));
		   if(!saveGiphyItem($json->data))
		      $j++;
		} else
		    $i++;
	}

}

function saveTenorItem($item){
	global $gif_db_helper;
	global $upload;
	echo $item->h1_title;
         //echo $item->images->original->url;
         //giphy can have media media0 media1 so different urls. Therefore isUrlInDB isn't enough"
                echo $item->url;
                if($gif_db_helper->isUrlInDB($item->url)){
                        echo "already in db";
                        return false;
                }   
                $hasNew = true;
                $result = $upload->downloadGif($item->media[0]->gif->url, $item->media[0]->webm->url);
                if($result["status"] != 0){
                        echo "error";
                        return true;
                }   
                echo $gif_db_helper->addToDb($result['url'], array(),'', $item->h1_title,$item->long_title, '',$item->url, $result['webm']);

	return true;
}

function crawlTenor(){
	global $gif_db_helper;
	global $upload;
	$i="";
	$j=0;
	$hasNew = false;
	while(true){
		$hasNew = false;
		$json = json_decode(tor_file_get_contents("https://api.tenor.com/v1/trending?key=JJHDC7UK73EH&limit=50&pos=".$i));
		$i = $json->next;
		foreach($json->results as $item){
			saveTenorItem($item);

		}
		
	}

}

$gif_db_helper = new GifDBHelper();
$upload = new UploadHelper();
crawlGiphy();
?>
