<?php
/*
Plugin Name: WP Fancy Instagram
Plugin URI: https://fatesinger.com/77633
Description: Add an instagram photo wall
Version: 2.0.0
Author: Bigfa
Author URI: https://fatesinger.com
*/

define('WFI_VERSION', '2.0.0');
define('WFI_URL', plugins_url('', __FILE__));
define('WFI_PATH', dirname( __FILE__ ));



function wfi_get_save_images($id,$url){
    $e = ABSPATH .'cache/'. $id .'.jpg';
    if ( !is_file($e) ) copy(htmlspecialchars_decode($url), $e);
    $url = home_url('/').'cache/'. $id .'.jpg';
    return $url;
}


add_action('wp_ajax_nopriv_get_ins', 'get_ins_callback');
add_action('wp_ajax_get_ins', 'get_ins_callback');
function get_ins_callback()
{
    $id = $_GET["next_url"];
    $type = $_GET["type"];
    $output = $type == 'liked' ? wfi_get_liked_images_hack($id) : wfi_get_images_hack($id);
    echo json_encode($output);
    die;
}

function wfi_get_user_info($token){
    $cachekey = 'insusercache';
    if(get_transient($cachekey)){
        return get_transient($cachekey);
    } else {
        delete_transient($cachekey);
        $getjson = 'https://api.instagram.com/v1/users/self?access_token='. $token .'&callback=?';
        $content = file_get_contents($getjson);
        $user = json_decode($content, true);
        set_transient($cachekey,$user['data'],60*60);
        return $user['data'];
    }

}

function wfi_header(){
    $user = wfi_get_user_info(wfi_get_setting('token'));
    $content = '<div class="instagram-header"><img class="instagram-avatar" src="' . $user['profile_picture'] . '"/><div class="instagram-header--right"><div class="instagram-user--name">' . $user['username']. '</div><div class="counts"><span class="counts"><strong>' . $user['counts']['media']. '</strong> 张照片</span><span class="follows">正在关注 <strong>' . $user['counts']['follows']. '</strong></span><span class="followed_by"><strong>' . $user['counts']['followed_by']. '</strong> 人关注</span></div><p class="instagram-user--bio">' . $user['bio']. '</p></div></div>';
    return $content;
}

function wfi_get_images_hack($id = null){
    $cachekey = 'inscache' . $id;
    if(get_transient($cachekey)){
        return get_transient($cachekey);
    } else {
        delete_transient($cachekey);
        $num = wfi_get_setting('number') ? wfi_get_setting('number') : 16;
        $url = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . wfi_get_setting('token') . '&count=' . $num .'&max_id=' . $id;
        $output = array();
        $cache_images = array();
        $content = file_get_contents($url);
        $data = json_decode($content,true);
        if($data['meta']['code'] == 200){
            $images = $data['data'];
            foreach($images as $fuck){
                $url = str_replace('s150x150/','',$fuck['images']['thumbnail']['url']);
                array_push($cache_images,wfi_get_save_images($fuck['id'],$url));
            }
            $next_url = $data['pagination']['next_max_id'];
            $output = array('next_url'=>$next_url,'images'=>$cache_images);
        }
        set_transient($cachekey,$output,60*60);
        return $output;
    }
}

function wfi_get_liked_images_hack($id = null){
    $cachekey = 'inslikecache' . $id;
    if(get_transient($cachekey)){
        return get_transient($cachekey);
    } else {
        delete_transient($cachekey);
        $num = wfi_get_setting('number') ? wfi_get_setting('number') : 16;
        $url = 'https://api.instagram.com/v1/users/self/media/liked?access_token=' . wfi_get_setting('token') . '&count=' . $num .'&max_like_id=' . $id;
        $output = array();
        $cache_images = array();
        $content = file_get_contents($url);
        $data = json_decode($content,true);
        if($data['meta']['code'] == 200){
            $images = $data['data'];
            foreach($images as $fuck){
                $url = str_replace('s150x150/','',$fuck['images']['thumbnail']['url']);
                array_push($cache_images,wfi_get_save_images($fuck['id'],$url));
            }
            $next_url = $data['pagination']['next_max_like_id'];
            $output = array('next_url'=>$next_url,'images'=>$cache_images);
        }
        set_transient($cachekey,$output,60*60);
        return $output;
    }
}


function wfi_nav( $type = 'recent'){
    global $post;
    if( $type == 'liked') {
        $content = '<div class="wfi-nav"><a class="ins-nav--item" href="' . get_permalink() . '">recent</a><span class="ins-nav--item is-active">liked</span></div>';
    } else {
        $content = '<div class="wfi-nav"><span class="ins-nav--item is-active">recent</span><a class="ins-nav--item" href="' . get_permalink() . '?type=liked">liked</a></div>';
    }

    return $content;
}

function wp_fancy_instagram(){
    if(wfi_get_setting('token')){
        $num = wfi_get_setting('number') ? wfi_get_setting('number') : 16;
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $output = '';
        $data = $_GET['type'] == 'liked' ? wfi_get_liked_images_hack() : wfi_get_images_hack();
        $images = $data['images'];
        foreach ($images as $image) {
            $output .= '<div class="wfi-image"><img data-action="zoom" src="'. $image .'"></div>';
        }
        echo wfi_header();
        if( wfi_get_setting('liked') ) echo wfi_nav($_GET['type']);
        echo '<div class="puma-instagram wfi-images">';
        echo $output;
        echo '</div><div class="wpi-loadmore u-textAlignCenter">';
        $type = $_GET['type'] == 'liked' ? 'liked' : 'recent';
        if( $data['next_url']) echo '<button class="wfi-btn" data-url="' . $data['next_url'] .'" data-type="' . $type .'">更多</button>';
        echo '</div>';
    }else{
        echo "请到后台设置输入自己的token";
    }
}

// Add shortcode
function wfi_shortcode($atts, $content=null){
    extract(shortcode_atts(array(), $atts));
    return wp_fancy_instagram();
}
add_shortcode('wfi','wfi_shortcode');

register_activation_hook(__FILE__, 'wfi_install');

function wfi_install(){
    // If path not exist, just create a new one
    $thumb_path = ABSPATH . "cache/";

    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }

}

function wfi_scripts(){
    wp_enqueue_style( 'wfi', WFI_URL . '/build/bundle.css' , array(), WFI_VERSION );
    wp_enqueue_script('jquery');
    wp_enqueue_script('wfi', WFI_URL . '/build/bundle.js', array(), WFI_VERSION ,true );
    wp_localize_script( 'wfi', 'WFI',array('url' =>admin_url('admin-ajax.php')) );

}
add_action('wp_enqueue_scripts', 'wfi_scripts', 20, 1);

add_action('admin_menu', 'wfi_menu');

function wfi_menu() {
    add_options_page('WFI 设置', 'WFI 设置', 'manage_options', basename(__FILE__), 'wfi_setting_page');
    add_action( 'admin_init', 'wfi_setting_group');
}

function wfi_setting_group() {
    register_setting( 'wfi_setting_group', 'wfi_setting' );
}

function wfi_setting_page(){
    @include 'include/wfi-setting.php';
}/**
 * 获取设置
 * @return [array]
 */
function wfi_get_setting($key=NULL){
    $setting = get_option('wfi_setting');
    return $key ? $setting[$key] : $setting;
}

/**
 * 删除设置
 * @return [void]
 */
function wfi_delete_setting(){
    delete_option('wfi_setting');
}

/**
 * [wfi_setting_key description]
 * @param  [type] $key [description]
 * @return [type]      [description]
 */
function wfi_setting_key($key){
    if( $key ){
        return "wfi_setting[$key]";
    }

    return false;
}
function wfi_update_setting($setting){
    update_option('wfi_setting', $setting);
}


