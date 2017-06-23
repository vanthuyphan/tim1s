    <div class="combo-change">
        <div class="listing-select">
            
        </div>                                        
    </div>
	
<div class="category-tools style-for-channel">
    <div class="view-sortby metadata-font font-size-1 ct-gradient">
			<?php 
			$pageURL = get_the_permalink();
            
            $sortby = isset($_GET['sortby']) ? esc_html($_GET['sortby']) : '';
            
            switch($sortby){
                case 'date':
                    echo esc_html__('Order By: &nbsp; Published date','videopro');
                    break;
                case 'view':
                    echo esc_html__('Order By: &nbsp; Views','videopro');
                    break;
                case 'like':
                    echo esc_html__('Order By: &nbsp; Like','videopro');
                    break;
                case 'comments':
                    echo esc_html__('Order By: &nbsp; Comments','videopro');
                    break;
                case 'title':
                    echo esc_html__('Order By: &nbsp; Title','videopro');
                    break;
                default:
                    echo esc_html__('Order By','videopro');
                    break;
            }
			?><i class="fa fa-angle-down"></i>
			<ul>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'date'), $pageURL )); ?>" title=""><?php echo esc_html__('Published date','videopro'); ?></a></li>
				<?php 
				
				$enable_sort_views = false;
				$enable_sort_likes = false;
				$videpro_extension = class_exists('Cactus_video');
				if($videpro_extension){
					$use_network_data = ot_get_option('ct_video_settings', 'use_video_network_data');
					$enable_sort_views = $use_network_data || function_exists('get_tptn_post_count_only');
					$enable_sort_likes = $use_network_data || function_exists('GetWtiLikeCount');
				}
				
				if($enable_sort_views){?>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'view'), $pageURL )); ?>" title=""><?php echo esc_html__('Views','videopro'); ?></a></li>
				<?php }
				
				if($enable_sort_likes){?>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'like'), $pageURL )); ?>" title=""><?php echo esc_html__('Like','videopro'); ?></a></li>
				<?php } ?>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'comments'), $pageURL )); ?>" title=""><?php echo esc_html__('Comments','videopro'); ?></a></li>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'title'), $pageURL )); ?>" title=""><?php echo esc_html__('Title','videopro'); ?></a></li>
			</ul>
                
    </div>
	<?php
    $videopro_blog_layout = videopro_global_blog_layout();
    $enable_switcher_toolbar = ot_get_option('enable_switcher_toolbar', 'on');
    if($enable_switcher_toolbar != 'off'){
        videopro_switcher_toolbar($videopro_blog_layout);
    }
    ?>
</div>

<div class="cactus-listing-wrap <?php echo $enable_switcher_toolbar != 'off' ? 'switch-view-enable' : '';?>">
    <div class="cactus-listing-config style-2 style-channel-listing"> <!--addClass: style-1 + (style-2 -> style-n)-->
			<?php 
            $cr_id_cn = get_the_ID();
            
            $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

			$use_network_data = osp_get('ct_video_settings', 'use_video_network_data');
			$use_network_data = ($use_network_data == 'on') ? 1 : 0;
            
            $post_status = 'publish';
    
            // author of channel can view all playlists
            $author_id = get_post_field('post_author', get_the_ID());
            if(get_current_user_id() == $author_id){
                $post_status = 'any';
            }
            
            $args = array(
                'post_type' => 'post',
                'post_status' => $post_status,
                'ignore_sticky_posts' => 1,
                'paged' => $paged,
                'orderby' => 'latest',
                'meta_query' => videopro_get_meta_query_args('channel_id', $cr_id_cn)
            );
            if(isset($_GET['sortby']) && $_GET['sortby'] == 'view'){
				if($use_network_data){
					$args = array_merge($args, array(
						'order' => 'DESC',
						'orderby' => 'meta_value_num',
						'meta_key' => '_video_network_views'
					));
				} else {
					$args['posts_per_page' ] = -1;
					$postlist = get_posts($args);
					$posts_id = array();
					foreach ( $postlist as $post ) {
					   $posts_id[] += $post->ID;
					}
					wp_reset_postdata();
					if(function_exists('videopro_get_tptn_pop_posts')){
						$args = array(
							'daily' => 0,
							'post_types' =>'post',
						);
						$ids = videopro_get_tptn_pop_posts($args);
					}
					$args = null;
					$args = array(
						  'post_type' => 'post',
						  'post__in'=> $ids,
						  'orderby'=> 'post__in',
						  'ignore_sticky_posts' => 1,
						  'paged' => $paged,
						  'meta_query' => videopro_get_meta_query_args('channel_id', $cr_id_cn)
					);
				}
            }elseif(isset($_GET['sortby'])&& $_GET['sortby']=='comments'){
                $args['orderby']= 'comment_count';
            }elseif(isset($_GET['sortby'])&& $_GET['sortby']=='title'){
                $args['orderby']= 'title';
				$args['order']= 'ASC';
            }elseif(isset($_GET['sortby'])&& $_GET['sortby']=='like'){
				if($use_network_data){
					$args = array_merge($args, array(
												'order' => 'DESC',
												'orderby' => 'meta_value_num',
												'meta_key' => '_video_network_likes'
											));
				} else {
					global $videopro_wpdb;	
					$time_range = 'all';
	
					$order_by = 'ORDER BY like_count DESC, post_title';
					$show_excluded_posts = get_option('wti_like_post_show_on_widget');
					$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
					
					if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
						$where = $videopro_wpdb->prepare("AND post_id NOT IN (%s)", get_option('wti_like_post_excluded_posts'));
					}
					else {$where = '';}
					$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$videopro_wpdb->prefix}wti_like_post` L, {$videopro_wpdb->prefix}posts P ";
					$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id $order_by";
					$posts = $videopro_wpdb->get_results($query);
	
					$p_data = array();
	
					if(count($posts) > 0) {
						foreach ($posts as $post) {
							$p_data[] = $post->post_id;
						}
					}
	
					$args = array_merge($args, array(
												'orderby'=> 'post__in',
												'order' => 'ASC',
												'post__in' =>  $p_data
											));
				}
			}
            
            $list_query = new WP_Query( $args );
            
            $it = $list_query->post_count;

            if($list_query->have_posts()){?>
    
            <div class="cactus-sub-wrap">
                <?php while($list_query->have_posts()){
                    $list_query->the_post();
                    get_template_part( 'html/loop/content', get_post_format()  );
                }?>
            </div>
            <?php } else {
                esc_html_e("There isn't any video in this channel","videopro");
            }
            
            videopro_paging_nav('.cactus-sub-wrap','html/loop/content', false, $list_query);

            wp_reset_postdata();
            ?>
    </div>
</div>
    
    
