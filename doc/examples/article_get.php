<?php 


// GET ONE ARTICLE

$article = Article::get(15);

print_r($article);



// GET ALL ARTICLES 

// Using yieldAll() method: 
$articles = Article::chunks();
foreach ($articles->yieldAll() as $id => $article) {
    // do something.
}


// Alternative way (not recommended, for complexity):
$articles = Article::chunks();
$articles->first();
$list = $articles->get(); // array of Articles
try {
    $articles->next(); 
    $list = $list + $articles->get();
    while (true) {
        $list = $list + $articles->getNext(); // array of Articles.
    }
} catch (\OutOfBoundsException $e) {
    // end of data,
}
foreach ($list as $id => $article) {
    // do something.
} 
