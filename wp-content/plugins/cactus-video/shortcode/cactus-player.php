<?php
// check if a post has JW Player settings. If yes, we use it, rather than default video URL 
function cactus_does_post_has_jwplayer_settings( $post_id = 0){
	if(osp_get('ct_video_settings','use_jwplayer_settings') == 1){
			return true;
	}
	
	return false;
}

function parse_cactus_player($atts, $content){
    if(isset($atts['id'])){
        $post_id = $atts['id'];
    } else {
        global $post;
        $post_id = $post->ID;
    }
	
	$file = get_post_meta($post_id, 'tm_video_file', true);
	global $url;
	$url = trim(get_post_meta($post_id, 'tm_video_url', true));
	
	$code = trim(get_post_meta($post_id, 'tm_video_code', true));
	$multi_link = get_post_meta($post_id, 'tm_multi_link', true);

	// trick to use JW Player default settings
	if($file == '' && cactus_does_post_has_jwplayer_settings($post_id)){
		$file = get_post_meta($post_id, '_jwppp-video-url-1', true);
	}
	
	$is_iframe = false;
	
	global $link_arr;
	if(!empty($multi_link)){
		$link_arr = videopro_build_multi_link($multi_link, false);
		//check request
		if(isset($atts['link']) && isset($link_arr[$atts['link']])){
			$url = trim($link_arr[$atts['link']]['url']);
		} else {
			if(isset($_GET['link']) && $_GET['link'] !== ''){
				$url = trim($link_arr[$_GET['link']]['url']);
			}
		}
	}
	
	if(strpos($code, 'iframe') !== false || strpos($code, 'object') || strpos($code, 'embed')) $is_iframe = true;
	
	$auto_load = osp_get('ct_video_settings','auto_load_next_video');
	$auto_load_prev = osp_get('ct_video_settings','auto_load_next_prev');
	
    if(!isset($atts['autoplay'])){
        $auto_play = osp_get('ct_video_settings','auto_play_video');
    } else {
        $auto_play = $atts['autoplay'];
    }
	$delay_video = osp_get('ct_video_settings','delay_video');
	
	if($delay_video == ''){
		$delay_video = 1;
	}
	
	$delay_video = (int)$delay_video * 1000;

	$onoff_related_yt = osp_get('ct_video_settings','onoff_related_yt');
	$onoff_html5_yt = osp_get('ct_video_settings','onoff_html5_yt');
	$using_yt_param = osp_get('ct_video_settings','using_yout_param');
	$onoff_info_yt = osp_get('ct_video_settings','onoff_info_yt');
	$allow_full_screen = osp_get('ct_video_settings','allow_full_screen');
	$allow_networking = osp_get('ct_video_settings','allow_networking');
	$remove_annotations = osp_get('ct_video_settings','remove_annotations');
	$interactive_videos = osp_get('ct_video_settings','interactive_videos');
    
    
	$single_player_video = osp_get('ct_video_settings','single_player_video');
	$social_locker = get_post_meta($post_id, 'social_locker', true);
	$video_ads_id = get_post_meta($post_id, 'ads_id', true);
	$enable_video_ads = class_exists('cactus_ads') ? osp_get( 'ads_config', 'enable-ads' ) : 'no';
    $enable_video_ads = apply_filters('videopro_enable_video_ads_in_player', $enable_video_ads, $post_id);

	global $is_auto_load_next_post;

	$player_embed = $is_auto_load_next_post != '1' ? 'player-embed' : 'player-embed1';

	$video_source = '';
	$main_video_url = '';
	$main_video_id = '';
	
	if(!$is_iframe){
		if(strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
			$video_source = 'youtube';
			$main_video_id = extractIDFromURL($url);
		}
		else if(strpos($url, 'vimeo.com') !== false) {
			$video_source = 'vimeo';
			$main_video_id = extractIDFromURL($url);
		}
        else if(strpos($url, 'dailymotion.com') !== false){
            $video_source = 'dailymotion';
            $main_video_id = extractIDFromURL($url);
        } 
		else if($file != '') {
			$video_source = 'self-hosted';
			$main_video_url = $file;
		}
	}
	
    $force_using_jwplayer7 = osp_get('ct_video_settings','youtube_force_jwplayer7');
    
    ob_start();

	$player_logic = get_post_meta($post_id, 'player_logic', true);
	if((strpos($player_logic, '[sociallocker') !== false) || (strpos($player_logic, '[/sociallocker]') !== false)){
		$auto_play = '0';
	}
	
	$player_logic_alt = get_post_meta($post_id, 'player_logic_alt', true);
	$id_vid = trim(get_post_meta($post_id, 'tm_video_id', true));

	
		if( in_array($auto_load, array('1','2','3'))){
            ?>
            <script language="javascript" type="text/javascript">
            function nextVideoAndRepeat(delayVideo){
                videopro_allow_next = true;
                if(jQuery('.autoplay-elms').length > 0){
                    if(!jQuery('.autoplay-elms').hasClass('active')){
                        videopro_allow_next = false;
                    }
                }
                
                if(videopro_allow_next){
                    setTimeout(function(){
                        var nextLink;
                        
                        if(jQuery('.video-toolbar-content .next-video').length > 0){
                            /* get url of .next-post link */
                            nextLink = jQuery('.video-toolbar-content .next-video').attr('href');
                        } else {
                            /** find next link in playlist **/
                            var itemNext = jQuery('.cactus-post-item.active');
                            if(itemNext.next().length > 0) {
                                nextLink = itemNext.next().find('.cactus-post-title').find('a').attr('href');
                            }else{
                                nextLink = jQuery('.cactus-post-item', '.cactus-sub-wrap').eq(0).find('.cactus-post-title').find('a').attr('href');
                            };
                        }
                        if(nextLink != '' && nextLink != null && typeof(nextLink)!='undefined'){ window.location.href = nextLink; }
                    }, delayVideo);
                }
            };
        </script>
            <?php
            if($video_source == 'vimeo'){
                if(class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '' ) && $is_auto_load_next_post != 1):
                    // do nothing
                else: 
                ?>
                <script src="//f.vimeocdn.com/js/froogaloop2.min.js"></script>
                <script>
                    jQuery(document).ready(function() {
                        jQuery('iframe').attr('id', 'player_1');
            
                        var iframe = jQuery('#player_1')[0],
                            player = $f(iframe),
                            status = jQuery('.status_videos');
            
                        /* When the player is ready, add listeners for pause, finish, and playProgress*/
                        player.addEvent('ready', function() {
                            status.text('ready');
            
                            player.addEvent('pause', onPause);
                            <?php if ($auto_load == '1' || $auto_load == '2' || $auto_load == '3'){?>
                            player.addEvent('finish', onFinish);
                            <?php }?>
                            /*player.addEvent('playProgress', onPlayProgress);*/
                        });
            
                        /* Call the API when a button is pressed*/
                        jQuery(window).load(function() {
                            player.api(jQuery(this).text().toLowerCase());
                        });
            
                        function onPause(id) {
                        }
            
                        function onFinish(id) {
                            var link = '';
                            nextVideoAndRepeat(<?php echo $delay_video ?>);
                        }
                    });
                </script>
	<?php       endif; 
            } elseif($video_source == 'dailymotion'){?>
			<script>
				/* This code loads the Dailymotion Javascript SDK asynchronously.*/
				(function() {
					var e = document.createElement('script'); e.async = true;
					e.src = document.location.protocol + '//api.dmcdn.net/all.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s);
				}());
			
				/* This function init the player once the SDK is loaded*/
				window.dmAsyncInit = function()
				{
					/* PARAMS is a javascript object containing parameters to pass to the player if any (eg: {autoplay: 1})*/
					var player = DM.player("player-embed", {video: "<?php echo $main_video_id; ?>", width: "900", height: "506", params:{<?php if($auto_play=='1'){?>autoplay :1, <?php } if($onoff_info_yt == '1'){?> info:0, <?php } if($onoff_related_yt== '1'){?> related:0 <?php }?>}});
			
					/* 4. We can attach some events on the player (using standard DOM events)*/
					player.addEventListener("ended", function(e)
					{
						nextVideoAndRepeat(<?php echo $delay_video; ?>);
					});
				};
			</script>
    <?php } elseif($video_source == 'youtube' && !$force_using_jwplayer7){?>
            <script src="//www.youtube.com/player_api"></script>
            <script>
                
                /* create youtube player*/
                var player;
                function onYouTubePlayerAPIReady() {
                    player = new YT.Player('player-embed', {
                      height: '506',
                      width: '900',
                      videoId: '<?php echo extractIDFromURL($url); ?>',
                      <?php if($onoff_related_yt != '0' || $onoff_html5_yt == '1' || $remove_annotations != '1' || $onoff_info_yt == '1'){ ?>
                      playerVars : {
                         <?php if($remove_annotations != '1'){?>
                          iv_load_policy : 3,
                          <?php }
                          if($onoff_related_yt == '1'){?>
                          rel : 0,
                          <?php }
                          if($onoff_html5_yt == '1'){
                          ?>
                          html5 : 1,
                          <?php }
                          if($onoff_info_yt == '1'){
                          ?>
                          showinfo:0,
                          <?php }?>
                      },
                      <?php }?>
                      events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                      }
                    });
                };
                /* autoplay video*/
                function onPlayerReady(event) { if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
                    <?php if($auto_play == 'on' || $auto_play == 1){?>
                    event.target.playVideo(); 
                    <?php }?>
                    }
                };   
                /* when video ends*/
                function onPlayerStateChange(event) {
                    if(event.data === 0) {
                        nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
                    };
                };        
            </script>
            <?php
          } elseif($video_source == 'self-hosted'){?>
              <script type='text/javascript'>
              jQuery(document).ready(function() {
                jQuery('#player-embed video').on('ended', function(){
                    nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
                });
              });
            </script>
             <?php
             
          }
      } // end autoplay
    

wp_reset_postdata();
//endwhile;
?>

<div id="player-embed" <?php if($video_source == 'youtube') { echo 'class="fix-youtube-player"'; }?>>
    <?php
        if((!$is_iframe && strpos($url, 'wistia.com') !== false )|| (strpos($code, 'wistia.com') !== false ) ){
            $id = substr($url, strrpos($url,'medias/')+7);
            ?>
            <div id="wistia_<?php echo $id ?>" class="wistia_embed" style="width:900px;height:506px;" data-video-width="900" data-video-height="506">&nbsp;</div>
            <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js"></script>
            <script>
            wistiaEmbed = Wistia.embed("<?php echo $id ?>", {
              version: "v1",
              videoWidth: 900,
              videoHeight: 506,
              volumeControl: true,
              controlsVisibleOnLoad: true,
              playerColor: "688AAD",
              volume: 5
            });
            </script>
            <?php
        } else {
             if($video_source == 'youtube' && ($using_yt_param == 1)){?>
             <div class="obj-youtube">
                <object width="900" height="506">
                <param name="movie" value="//www.youtube.com/v/<?php echo $main_video_id; ?><?php if($onoff_related_yt != '0'){?>&rel=0<?php }if($auto_play == '1'){?>&autoplay=1<?php }if($onoff_info_yt == '1'){?>&showinfo=0<?php }if($remove_annotations != '1'){?>&iv_load_policy=3<?php }if($onoff_html5_yt == '1'){?>&html5=1<?php }?>&wmode=transparent" ></param>
                <param name="allowFullScreen" value="<?php if($allow_full_screen!='0'){?>true<?php }else {?>false<?php }?>"></param>
                <?php if($interactive_videos == 0){?>
                <param name="allowScriptAccess" value="samedomain"></param>
                <?php } else {?>
                <param name="allowScriptAccess" value="always"></param>
                <?php }?>
                <param name="wmode" value="transparent"></param>
                <embed src="//www.youtube.com/v/<?php echo $main_video_id;if($onoff_related_yt != '0'){?>&rel=0<?php }if($auto_play == '1'){?>&autoplay=1<?php }if($onoff_info_yt=='1'){?>&showinfo=0<?php }if($remove_annotations != '1'){?>&iv_load_policy=3<?php }if($onoff_html5_yt == '1'){?>&html5=1<?php }?>"
                  type="application/x-shockwave-flash"
                  allowfullscreen="<?php if($allow_full_screen!='0'){?>true<?php }else {?>false<?php }?>"
                  <?php if($allow_networking=='0'){ ?>
                  allowNetworking="internal"
                  <?php }?>
                  <?php if($interactive_videos==0){?>
                  allowscriptaccess="samedomain"
                  <?php } else {?>
                  allowscriptaccess="always"
                  <?php }?>
                  wmode="transparent"
                  width="100%" height="100%">
                </embed>
                </object>
                </div>
             <?php
             } else {
                tm_video($post_id, $auto_play == '1' ? true : false, (!empty($multi_link) && $is_iframe ? $url : ''));
             }
         }
    ?>            
</div><!--/player-->
<?php
    //social locker
    $player_html = ob_get_contents();
    ob_end_clean();
    

    if(class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '' ) && $is_auto_load_next_post != 1)
    {
        
    	$player_html = apply_filters('cactus_player_html','', $player_html, $post_id);
        
        if($video_source == 'vimeo'){
            //$player_html .= '<script src="//f.vimeocdn.com/js/froogaloop2.min.js"/></script>';
            $player_html .= '<script src="https://player.vimeo.com/api/player.js"/></script>';
        }
    }
    
    if(!strpos($player_logic, '[player]')===false){ //have shortcode
        $player_html = do_shortcode(str_replace("[player]",$player_html,$player_logic));
    } elseif($player_logic) {
        $player_logic = "return (" . $player_logic . ");";
        if( eval($player_logic) ){
            // player logic is true, so do nothing
        } else {
            return '<div class="player-logic-alt">'.do_shortcode($player_logic_alt).'</div>';
        }
    }
    
    
		/* this is for Cactus-Ads plugin */
		$player_html .= '<input type="hidden" name="main_video_url" value="' . ($video_source != 'self-hosted' ? $main_video_id : $main_video_url) . '"/><input type="hidden" name="main_video_type" value="' . $video_source . '"/>';
	
    
    return $player_html;
}

add_shortcode( 'cactus_player', 'parse_cactus_player' );