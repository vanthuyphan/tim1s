<?php
class Videopro_Widget_Categories extends WP_Widget {

	/**
	 * Sets up a new Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'videopro_widget_categories widget_casting',
			'description' => esc_html__( 'A list of categories.','videopro' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'videopro_categories', esc_html__( 'VideoPro - Categories','videopro' ), $widget_ops );
	}
    
    private function show_category($cat_args, $instance, $hierarchical = false){
        $c = ! empty( $instance['count'] ) ? '1' : '0';
		$layout 			= empty($instance['layout']) ? '' : $instance['layout'];
        
        $data_cat  = get_categories ($cat_args);
        
        // hold categories list to check for current id
        $parents = array();
        if(is_category()){
                
            $current_cat = get_category( get_query_var( 'cat' ) );
            $current_id = $current_cat->cat_ID;
            array_push($parents, $current_id);
            
            while($current_cat->parent != 0){
                $current_cat = get_category($current_cat->parent);
                array_push($parents, $current_cat->cat_ID);
            }
        }
        
        foreach ( $data_cat as $category ) {
            $cat_id = $category->term_id;
            $cat_icon = get_option("cat_icon_$cat_id") ? get_option("cat_icon_$cat_id"):'';
            $cat_img = '';
            $cat_url = get_category_link( $cat_id );
			
			$cat_img = videopro_get_category_thumbnail( $category->term_id, 'small');
            
            // check current item
            $current_class = '';
            if(in_array($cat_id, $parents)){
                $current_class = 'current';
            }
            
            ?>
            <div class="channel-subscribe <?php echo esc_attr($current_class);?>">
                <?php if($cat_img != '' && $layout == 'thumb'){?>
                <div class="channel-picture">
                    <a href="<?php echo esc_url($cat_url);?>" title="<?php echo esc_attr( $category->cat_name );?>">
                        <span class="category-bg" style="background-image:url(<?php echo esc_url($cat_img);?>)"></span>
                    </a>
                </div>
                <?php }?>
                <div class="channel-content">
                    <h4 class="channel-title h6">
                        <a href="<?php echo esc_url($cat_url);?>" title="<?php echo esc_attr( $category->cat_name );?>">
                            <?php if($layout!='thumb' && $cat_icon!=''){?>
                                <i class="fa <?php echo esc_attr($cat_icon);?>"></i>
                            <?php } ?>
                            <?php echo esc_html( $category->cat_name );?>
                        </a>
                        <?php if($c){?>
                        <span class="tt-number">(<?php echo esc_html( $category->category_count );?>)</span>
                        <?php }?>
                    </h4>
                </div>
                <?php 
                    if($hierarchical || $current_class == 'current'){
                        $cat_args['parent'] = $cat_id;
                        $this->show_category($cat_args, $instance, $hierarchical);
                    }
                    ?>
            </div>
            <?php
        }
    }

	/**
	 * Outputs the content for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Categories widget instance.
	 */
	public function widget( $args, $instance ) {

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array(
			'orderby'      => 'name'
		);
        
        $h = ! empty( $instance['hierarchical'] ) ? true : false;

		?>
		<div class="widget_casting_content <?php if($layout != 'thumb'){?>widget-cat-style-icon<?php }?>">
        	<div class="post-metadata sp-style style-2 style-3">
                <?php
                $cat_args['title_li'] = '';
                
                $cat_args['parent'] = 0;
                
                $this->show_category($cat_args, $instance, $h);

                ?>
        	</div>
		</div>
		<?php

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['layout'] = esc_attr($new_instance['layout']);

		return $instance;
	}

	/**
	 * Outputs the settings form for the Categories widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = sanitize_text_field( $instance['title'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$layout = isset($instance['layout']) ? esc_attr($instance['layout']) : '';
		?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:','videopro' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e( 'Show post counts','videopro' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php esc_html_e( 'Show all sub-categories','videopro' ); ?></label></p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id("layout")); ?>">
				<?php esc_html_e('Layout','videopro');	 ?>:
                <span style="font-style: italic; display:block;"><?php esc_html_e('choose widget layout','videopro');?></span>
                <select id="<?php echo esc_attr($this->get_field_id("layout")); ?>" name="<?php echo esc_attr($this->get_field_name("layout")); ?>">
                    <option value="icon"<?php selected( $layout, "icon" ); ?>><?php esc_html_e('Show category icon','videopro');?></option>
                    <option value="thumb"<?php selected( $layout, "thumb" ); ?>><?php esc_html_e('Show category thumbnail','videopro');?></option>
                </select>
            </label>
        </p>
		<?php
	}

}
// register  widget
add_action( 'widgets_init', create_function( '', 'return register_widget("Videopro_Widget_Categories");' ) );