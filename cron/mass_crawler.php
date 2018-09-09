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
    $content = file_get_contents($url ,false,$context); 
     
    //If http response header mentions that content is gzipped, then uncompress it
    foreach($http_response_header as $c => $h)
    {
        if(stristr($h, 'content-encoding') and stristr($h, 'gzip'))
        {
            //Now lets uncompress the compressed data
            $content = gzinflate( substr($content,10,-8) );
        }
    }
     
    return $content;
}
$gif_db_helper = new GifDBHelper();
$upload = new UploadHelper();
$i=0;
while(true){
	$json = json_decode(get_url("https://api.giphy.com/v1/gifs/trending?api_key=3eFQvabDx69SMoOemSPiYfh9FY0nzO9x&sort=desc&offset=".($i*25)));
	foreach($json->data as $item){
		echo $item->title;
		echo $item->images->original->url;
		//giphy can have media media0 media1 so different urls. Therefore isUrlInDB isn't enough"
		if(count($gif_db_helper->getByGifFileName(basename($item->images->original->url)))>0){
			echo "already in db";
			continue;
		}
		$result = $upload->downloadGif($item->images->original->url);
		if($result["status"] != 0){
			echo "error";
			continue;
		}
		echo $gif_db_helper->addToDb($result['url'], array(),'', $item->title,$item->title, '',$item->images->original->url, $result['webm']);

	}
	$i++;
}
?>
