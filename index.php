<?php
        
function getGammaProduct($ean_code){
        $gamma_base = "https://www.gamma.be/nl/assortiment/zoeken?text=";
        $html = file_get_contents($gamma_base . str_replace(' ', '', $ean_code));

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
                if( $article->getAttribute('data-ean') == str_replace(' ', '', $ean_code)){
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

        return isset($article_meta['name']) ? $article_meta : NULL ;
}


if (isset($_POST['ean_code']))
        $product_data = getGammaProduct($_POST['ean_code']);

?>

<h1>Search Product </h1>
<form method="POST" action="">
        <input type="text" name="ean_code" value="">
        <input type="submit" name="submit" value="Search">
</form>

<?php  if (isset($_POST['ean_code']) && $product_data != NULL){ ?>

        <h2>Result</h2>
        <table>
        <tr> <th>Name</th> <td> <?php echo $product_data['name'] ?> </td> </tr>
        <tr> <th>Image</th> <td> <img src="<?php echo $product_data['img'] ?>" /> </td> </tr>
        <tr> <th>Price</th> <td> <?php echo $product_data['price'] ?> </td> </tr>
        </table>

<?php } ?>

