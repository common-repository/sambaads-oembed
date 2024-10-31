<?php
/*
Plugin Name: SambaAds oEmbed
Plugin URI: http://app.sambaads.com/sambaads-oembed-wordpress-plugin
Description: Adds oEmbed support for sambaads.com in WordPress posts, pages and custom post types. There are no settings. Simply, add the Screenr video URL in your content editor.
Version: 1.0.5
Author: Lucas Nogueira
License: GPL2
*/
add_action( 'init', function()
{
    wp_embed_register_handler( 
        'forbes', 
        '#https?://(player\.)?sambaads\.com/.*#i', 
        'wp_embed_handler_forbes' 
    );
    
    add_filter('oembed_fetch_url','add_args_amp',10,3);
       
    $oembedUrls = array(
        array(
            'format' => '#https?://(player\.)?sambaads\.com/.*#i',
            'provider' => 'http://player.sambaads.com/services/oembed',
            'regex' => true),
    );
    foreach ($oembedUrls as $oembed) {
        wp_oembed_add_provider($oembed['format'], $oembed['provider'], $oembed['regex']);
    }

} );

function add_args_amp($provider, $url, $args) 
 {
    preg_match('@.*&url=(.*?)&@i', $provider, $matches);
    $url = urldecode($matches[1]);

    $path = $_SERVER['REQUEST_URI'];
    $rest = substr($path, -5);
    
    if($rest == '/amp/'){
        $args = array(
            'url' => urlencode($url.'&type=wordpress'),
        );
       
        $provider = add_query_arg( $args, $provider );
    }

    return $provider;   
}

function wp_embed_handler_forbes( $matches, $attr, $url, $rawattr )
{
    $oEmbed = wp_oembed_get($url);
    
    return $oEmbed;
}
