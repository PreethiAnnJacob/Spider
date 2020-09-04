<?php
    /*Recursive web crawler*/
    $crawledLinks=array();//all crawled links
    function crawl($url,$depth=0){
        global $crawledLinks;
        $crawling=array();//crawled links from current crawling link 

        //Suppose that user won't search further
        if($depth>5){
            echo ("<div style='color:red;'>Crawler giving up</div></br>");
            return;
        }

        $options=array('http'=>array('method'=>'GET','user-agent'=>'MyBabyCrawler'));
        $context=stream_context_create($options);
        $doc=new DomDocument();
        @$doc->loadHTML(file_get_contents($url,false,$context));
        $links=$doc->getElementsByTagName('a');

        foreach ($links as $i){
            $link=$i->getAttribute('href');
            if(ignoreLink($link))   continue;
            $link=convertLink($url,$link);
            if(!in_array($link,$crawledLinks)){
                $crawledLinks[]=$link;
                $crawling[]=$link;
                echo ("<span style='margin-left:".(20*$depth)."px'>Inserting new link into database:-<span style='color:green'>$url</span></span></br>");
            }
        }
        foreach($crawling as $crawlUrl){
            echo ("<span style='color:grey;margin-left:".(20*$depth)."px;'>[+]Crawling <u>$crawlUrl</u></span></br>");
            crawl($crawlUrl,$depth+1);
        }
        if(count($crawling)==0)
            echo ("<span style='color:red;margin-left:".(20*$depth)."px;'>[!]Didn't find any links in <u>$url</u></span></br>");
    }

    //Check to consider only external links for further scraping
    function ignoreLink($url){
        return $url=="" or $url[0]=='#' or substr($url,0,11)=="javascript:";
    }

    //Convert Relative path to Absolute path
    function convertLink($url,$path){
        if (substr_compare($path,"//",0,2)==0)
            return parse_url($site)['scheme'].$path;
        elseif(substr_compare($path,"http://",0,7)==0 or substr_compare($path,"https://",0,8)==0 or
            substr_compare($path,"www.",0,4)==0)
            return $path;
        else
            return $url.'/'.$path;
    }

    crawl("http://google.com");
?>
