<?php
eval(`cv php:boot`);
use Civi\DonCorps\Install\SearchUpdateToken;

$return = SearchUpdateToken::updateToken("managed/tags_searches.txt");

//\Civi\DonCorps\Install\SearchUpdateToken::updateToken(__DIR__ . '/managed/tags_searches.txt');

if($return){
    \Civi::log()->warning("don_corps : OK");

}else{
       \Civi::log()->warning("don_corps : KO");
 
};
