<?php
	/*Recursive Crawler reading metadata of all the links it pass through in the input website*/
	$crawledLinks=array();

	function crawl($url,$depth=0){
		global $crawledLinks;
		$crawling=array();
		if($depth>5){
			echo "<div style='color:red;'>Crawler giving up</div>";
			return;
		}
		$options=array('http'=>array('method'=>'GET','user-agent'=>'MyBabyCrawler'));
		$context=stream_context_create($options);
		$doc=new DomDocument();
		@$doc->loadHTML(file_get_contents($url,false,$context));
		$links=$doc->getElementsByTagName('a');
		$pageTitle=getDocTitle($doc,$url);
		$metaData=getDocMetaData($doc);
		foreach ($links as $i){
			$link=$i->getAttribute('href');
			if(ignoreLink($link))	continue;
			$link=convertlink($url,$link);
			if(!in_array($link,$crawledLinks)){
				$crawledLinks[]=$link;
				$crawling[]=$link;
				echo ("Inserting new record {URL = ".$url.", Title = ".$pageTitle.", Description = ".$metaData['description'].", Keywords= ".$metaData['keywords']."}</br>");;
			}
		}
		foreach($crawling as $crawlUrl)
			crawl($crawlUrl,$depth+1);
	}

	function getDocTitle(&$doc,$url){
		$titleNodes=$doc->getElementsByTagName('Title');
		//If title is not present, take Title=Url
		if(count($titleNodes)==0 or !isset($titleNodes[0]->nodeValue))	return $url;
		$title=str_replace('','\n',$titleNodes[0]->nodeValue);
		//return (((strlen($title)<1)) ? $url : $title);
		return $title;
	}

	function getDocMetaData(&$doc){
		$metaData=array();
		$metaNodes=$doc->getElementsByTagName('meta');
		foreach($metaNodes as $node)
			$metaData[$node->getAttribute('name')]=$node->getAttribute('content');
		if(!isset($metaData['description']))
			$metaData['description']='No description available';
		if(!isset($metaData['keywords']))
			$metaData['keywords']='';
		return array(
			'keywords'=>str_replace('','\n',$metaData['keywords']),
			'description'=>str_replace('','\n',$metaData['description'])
		);
	}
	function ignoreLink($url){
		return $url=='' or $url[0]=='#' or substr($url,0,11)=='javascript:';
	}

	function convertLink($url,$link){
		if(substr_compare($link,'//',0,2)==0)
			return parse_url($url)['scheme'].$link;
		if (substr_compare($link,'http://',0,7)==0 or substr_compare($link,'https://',0,8)==0 or substr_compare($link,"www.",0,4)==0)
			return $link;
		return $url.'/'.$link;
	}
	crawl("https://en.wikipedia.org/wiki/Hello");
	//crawl("http://example.com/");
?>
