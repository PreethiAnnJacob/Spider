<?php
	/* Non recursive Web crawler*/
	function crawl($url){
		$options=array('http'=>array('method'=>'GET','user-agent'=>'MyBabyCrawler'));
		$context=stream_context_create($options);
		$doc=new DomDocument();
		@$doc->loadHTML(file_get_contents($url,false,$context));//false to avoid crawling php.ini config file
		$links=$doc->getElementsbyTagName('a');
		foreach($links as $i)
			echo $i->getAttribute('href').'<br/>';
	}

	//Enter the url to be crawled
	crawl("http://wikipedia.org/")
?>
