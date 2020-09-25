<?php
        
$ean_code = "5010646061186";

function getGammaProduct($ean_code){
        $gamma_base = "https://www.gamma.be/nl/assortiment/zoeken?text=";
        $html = file_get_contents($gamma_base . $ean_code);

        /*** a new dom object ***/ 
        $dom = new DOMDocument; 

        /*** load the html into the object ***/ 
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        /*** discard white space ***/ 
        $dom->preserveWhiteSpace = false; 

        /*** the table by its tag name ***/ 
        $articles = $dom->getElementsByTagName('article'); 

        /*** Variable to store all gatheredmeta data ***/ 
        $article_meta = [];

        /*** loop over the table rows ***/ 
        foreach ($articles as $article) {
                if( $article->getAttribute('data-ean') == $ean_code){
                        // Search all A for url
                        foreach ($article->getElementsByTagName('a') as $article_link) {
                                if($article_link->getAttribute('class') == "click-mask"){
                                        $article_meta['name'] = $article_link->getAttribute('title');
                                }
                        }

                        // Search all IMGS for src
                        foreach ($article->getElementsByTagName('img') as $article_img) {
                                if($article_img->getAttribute('alt') == $article_meta['name']){
                                        $article_meta['img'] = $article_img->getAttribute('src');
                                }
                        }

                        // Search all DIVS for price
                        foreach ($article->getElementsByTagName('div') as $article_div) {
                                if($article_div->getAttribute('class') == 'product-tile-price'){
                                        $article_meta['price'] = $article_div->nodeValue;
                                }
                        }

                        break;
                }
        }

        return $article_meta;
}


print("<strong>Product Data</strong>: ");
print_r(getGammaProduct($ean_code));