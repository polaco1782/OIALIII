<?php

define('CHANNEL_ID', '');
define('BOT_TOKEN', '');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN);

$find = urlencode('ford fusion');

// shut up warnings
error_reporting(E_ERROR | E_PARSE); // Remove HTML broken tag warnings

function text_telegram($msg)
{
    $data = [
        'chat_id' => CHANNEL_ID,
        'text' => $msg,
        'parse_mode' => 'Markdown'
    ];
    
    var_dump(file_get_contents(API_URL."/sendMessage?" . http_build_query($data)));
}

function photo_telegram($pic, $msg)
{
    $data = [
        'chat_id' => CHANNEL_ID,
        'photo' => $pic,
        'caption' => $msg,
        'parse_mode' => 'Markdown'
    ];
    
    var_dump(file_get_contents(API_URL."/sendPhoto?" . http_build_query($data)));
}

$url = 'https://rs.olx.com.br/?q='.$find;
$file = file_get_contents($url);
$dom = new DOMDocument();
$dom->loadHTML($file);
$xpath = new DomXPath($dom);
$data = $xpath->query('//div[div/@id="listing-main-content-slot"]//ul/li/div');

if(!dir('seen'))
    mkdir('seen');

foreach($data as $item)
{
    $thumb = $item->getElementsByTagName('img')->item(0)->getAttribute('src');
    $link = $item->getElementsByTagName('a')->item(0)->getAttribute('href');
    $name = $item->getElementsByTagName('h2')->item(0)->nodeValue;

    $text = '';
    $lenght = $item->getElementsByTagName('span')->length;
    for($i = 0; $i < $lenght; $i++)
        $text .= $item->getElementsByTagName('span')->item($i)->getAttribute('aria-label')."\n";

    if(!file_exists('seen/'.md5($link)))
    {
        file_put_contents('seen/'.md5($link), $link."\n".$text);
        photo_telegram($thumb, "[".$name."](".$link.")\n\n".$text."\n\n");
    }
}