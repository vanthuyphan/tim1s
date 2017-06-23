<?php

$id = get_the_ID();

$post_data = videopro_get_post_viewlikeduration($id);
extract($post_data);

if((isset($atts_sc['show_like']) && $atts_sc['show_like'] =='0')){
	$like ='';
}
if((isset($atts_sc['show_duration']) && $atts_sc['show_duration'] =='0')){
	$time_video ='';
}
$img_size = array(100,75);

$link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
if($i == 1 ){
?>
<div class="cactus-listing-config style-3"> <!--addClass: style-1 + (style-2 -> style-n)-->
    <div class="cactus-sub-wrap">                                                        
     <?php }?>   
        
        <!--item listing-->                                                
        <article class="cactus-post-item hentry">
        
            <div class="entry-content">                                        
                
                <!--picture (remove)-->
                <div class="picture">
                    <div class="picture-content">
                        <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>">
							<?php echo videopro_thumbnail($img_size);?>
                            <?php 
							$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
							$post_format = get_post_format($id);
							echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video small-icon"></div>' : '', $id, $post_format,$lightbox,'small-icon' );?>
                        </a>
                        
                        <?php 
						if((isset($atts_sc['show_rating']) && $atts_sc['show_rating'] !='0') || (!isset($atts_sc['show_rating']))){
						if(videopro_post_rating($id) != ''){?><?php echo videopro_post_rating($id); }
						}?>
                        
                        <?php if($like!=''){?><div class="cactus-note font-size-1"><i class="fa fa-thumbs-up"></i><span><?php echo $like;?></span></div><?php }
                        if($time_video!='00:00' && $time_video!='00' && $time_video!='' ){?>
                        <div class="cactus-note ct-time font-size-1"><span><?php echo $time_video;?></span></div>
                        <?php }
                        
                        do_action('scb-loop-item-picture-content', $id);?>                                                       
                    </div>                              
                </div><!--picture-->
                
                <div class="content">
                                                                                
                    <!--Title (no title remove)-->
                    <h3 class="cactus-post-title entry-title h6"> 
                        <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a>
                    </h3><!--Title-->                                                                    
                    
                    <div class="posted-on metadata-font">
                    	<?php if((isset($atts_sc['show_author']) && $atts_sc['show_author'] !='0') || (!isset($atts_sc['show_author']))){?>
                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="author cactus-info font-size-1"><span><?php echo esc_html( get_the_author() );?></span></a>
                        <?php }?>
                        
                        <?php if((isset($atts_sc['show_datetime']) && $atts_sc['show_datetime'] !='0') || (!isset($atts_sc['show_datetime']))){?>
                        <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $link_post); ?></div>
                        <?php }?>
                    </div> 
                    
                    <div class="posted-on metadata-font">                                                            	
                        <?php 
                        
                        if((isset($atts_sc['show_view_count']) && $atts_sc['show_view_count'] !='0') || (!isset($atts_sc['show_view_count']))){
                            if($viewed!=''){?><div class="view cactus-info font-size-1"><span><?php echo videopro_get_formatted_string_number($viewed);?></span></div><?php }
                        }
                        
						if((isset($atts_sc['show_comment_count']) && $atts_sc['show_comment_count'] !='0') || (!isset($atts_sc['show_comment_count']))){
						if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
                        <a href="<?php echo get_comments_link(); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" class="comment cactus-info font-size-1"><span><?php echo number_format_i18n(get_comments_number());?></span></a>
                        <?php }
						}?>
                    </div>
                    
                </div>
                
            </div>
            
        </article><!--item listing-->
                                                        
<?php if($i == $nbf){?>      
  </div>
</div>
<?php }