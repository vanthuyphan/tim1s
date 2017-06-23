<?php

/*
 * Walker for the Front End MashMenu
 */
class MashMenuWalkerCore extends Walker_Nav_Menu{

	protected $index = 0;
	protected $menuItemOptions;
	protected $noMashMenu;

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Calls parent function in wp-includes/class-wp-walker.php
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
		if ( !$element )
			return;

		//Add indicators for top level menu items with submenus
		$id_field = $this->db_fields['id'];
		$element->classes[] = 'level' . $depth;
		if ( $depth == 0 && !empty( $children_elements[ $element->$id_field ] ) ) {
			$element->classes[] = 'has-sub';
		}
		
		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		if($this->getMashMenuOption($element->menu_item_parent,'menu_style') == 'preview'){
		//if($this->getMashMenuOption($element->menu_item_parent,'isMega') != 'off'){
			if($depth == 1 && is_array($args[0]))
				$args[0]['parentMega'] = 'preview';
		} elseif(is_array($args[0])){
				$args[0]['parentMega'] = $this->getMashMenuOption($element->menu_item_parent,'menu_style');
		}
		
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		
		call_user_func_array(array($this, 'start_el'), $cb_args);

		$id = $element->$id_field;
				
		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id])) {
			if(isset( $children_elements[$id])){
				foreach( $children_elements[ $id ] as $child ){

					if ( !isset($newlevel) ) {
						$newlevel = true;
						//start the child delimiter
						
						$sidebar_name = $this->getMashMenuOption($element->$id_field,'addSidebar');
						$args = array(array("id"=>$element->$id_field,"title"=>$element->title,'addSidebar'=>$sidebar_name));
						
						
						if($depth == 0)
							$args[0]["parentMega"] = $this->getMashMenuOption($element->$id_field,'menu_style') ;
						else
							$args[0]["parentMega"] = $this->getMashMenuOption($element->menu_item_parent,'menu_style') ;
							
						
						$cb_args = array_merge( array(&$output, $depth), $args);
												
						call_user_func_array(array($this, 'start_lvl'), $cb_args);
					}
					$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
				}
				unset( $children_elements[ $id ] );
			}
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$args = array(array("id"=>$element->$id_field,"title"=>$element->title));
			
			$args[0]["parentMega"] = $this->getMashMenuOption($element->$id_field,'menu_style');
			
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array($this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'end_el'), $cb_args);
	}
	
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		if($depth == 0){
			if(isset($args["parentMega"]) && $args["parentMega"] == 'preview'){
				$output .= "\n$indent<div class=\"sub-content dropdown-menu sub-menu sub-menu-box sub-menu-box-tabs preview-mode\"><ul class=\"sub-channel\">";
			} elseif(isset($args["parentMega"]) && $args["parentMega"] == 'columns') {
				$output .= "\n$indent<div class=\"sub-content dropdown-menu sub-menu-box sub-menu-box-grid dark-div\"><ul class=\"columns\">\n";
			} else {
				$output .= "\n$indent<ul class=\"sub-menu dropdown-menu sub-menu-list level0\">\n";
			}
		} else {
			
			if(isset($args["parentMega"]) && $args["parentMega"] == 'columns'){
				$output .= "\n$indent<li><ul class=\"list\"><li class=\"header\">".$args["title"]."</li>\n";				
			} else {
				$output .= "\n$indent<ul class=\"sub-menu dropdown-menu level" . $depth . "\">\n";
			}
		}
	}
	
	function end_lvl( &$output, $depth = 0, $args = array() ){
		$indent = str_repeat( "\t", $depth );
		if($depth == 0){
			if(isset($args["parentMega"]) && $args["parentMega"] == 'preview'){
				$output .= "\n$indent</ul></div>"; // end <ul class="sub-channel">
			} elseif(isset($args["parentMega"]) && $args["parentMega"] == 'columns') {
				$output .= "\n$indent</ul></div>\n"; // end <ul class="columns">
			} else {
				$output .= "</ul>";
			}
		} else {
			if(isset($args["parentMega"]) && $args["parentMega"] == 'columns')
				$output .= "\n$indent</ul></li>\n";
			else
				$output .= "</ul>";
		}
	}

	function getMashMenuOption( $item_id , $id ){
		$option_id = 'menu-item-'.$id;

		//Initialize array
		if( !is_array( $this->menuItemOptions ) ){
			$this->menuItemOptions = array();
			$this->noMashMenu = array();
		}

		//We haven't investigated this item yet
		if( !isset( $this->menuItemOptions[ $item_id ] ) ){
			
			$mashmenu_options = false;
			if( empty( $this->noMashMenu[ $item_id ] ) ) {
				$mashmenu_options = get_post_meta( $item_id , '_mashmenu_options', true );
				if( !$mashmenu_options ) $this->noMashMenu[ $item_id ] = true; //don't check again for this menu item
			}

			//If $mashmenu_options are set, use them
			if( $mashmenu_options ){
				$this->menuItemOptions[ $item_id ] = $mashmenu_options;
			} 
			//Otherwise get the old meta
			else{
				$option_id = '_menu_item_'.$id;
				return get_post_meta( $item_id, $option_id , true );
			}
		}
		return isset( $this->menuItemOptions[ $item_id ][ $option_id ] ) ? stripslashes( $this->menuItemOptions[ $item_id ][ $option_id ] ) : '';
	}
	
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ){
		$args = (object)$args;
		
		// check display logic
		$display_logic = $this->getMashMenuOption( $item->ID, 'displayLogic' );
		if(($display_logic == 'guest' && is_user_logged_in()) || ($display_logic == 'member' && !is_user_logged_in())){
			return;
		}
		if(isset($classes)){
			unset($classes['list-style']);
		}
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
		//Handle class names depending on menu item settings
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$is_parent = '';
		
		foreach($classes as $class){
			if($class == 'parent'){
				$is_parent = true;
				$classes[]=$depth==0?'dropdown':'dropdown-submenu';
			}
		}
		if($depth==0){
			$classes[] = 'main-menu-item';
			if($this->getMashMenuOption( $item->ID, 'menu_style' ) == 'preview' || $this->getMashMenuOption( $item->ID, 'menu_style' ) == 'columns'){ $classes[] = 'dropdown-mega'; }
		}
		
		if($depth == 1 && $args->parentMega == 'preview'){
			$classes[] = 'channel-title';
		}
		if($depth == 0 && $opt_menu_style = $this->getMashMenuOption( $item->ID, 'menu_style' )== 'list'){
			$classes[] = 'list-style';
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' '. esc_attr( $class_names ) . '';

		$options = get_option('mashmenu_options');
		
		if($depth == 1 && $args->parentMega == 'preview'){
			$post_type = 'any';
			/* if you want exactly what kind of post types which belong to this category
			 * uncomment & edit code below
			 * ====================
			 * if($item->object = 'custom-taxonomy') $post_type = 'custom-post-type';
			 * ====================
			 */

			if($options['ajax_loading'] != 'on' || 1){
				$output .= '<li><div class="channel-content" id="channel-'.$item->ID.'"><div class="row">';
				
				$helper = new MashMenuContentHelper();
				
				switch($item->object){
					case 'category':
						$output .= $helper->getLatestCategoryItems($item->object_id);
						break;
					case 'post_tag':
						$output .= $helper->getLatestItemsByTag($item->object_id);
						break;
					case 'page':
						$output .= $helper->getPageContent($item->object_id);
						break;
					case 'post':
						$output .= $helper->getPostContent($item->object_id);
						break;
					case 'product_cat':
						$output .= $helper->getWProductItems($item->object_id);
						break;
					default:						
						$output .= $helper->getLatestCustomCategoryItems($item->object_id, $item->object,$post_type);
						break;
				}
				
				
				$output .= '</div></div></li>';
			}
			
			$output .= /*$indent . */'<li id="mega-menu-item-'. $item->ID . '"' . $value .' class="'. $class_names .'" data-target="channel-'.$item->ID.'" data-type="'.$item->type.'" data-post="'.$post_type.'" data-object="'.$item->object.'" data-id="'.$item->object_id.'">';
		} else if($depth != 1){
			$output .= /*$indent . */'<li id="mega-menu-item-'. $item->ID . '"' . $value .' class="'.$class_names.'">';
		}

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		//$attributes .= ! empty( $item->class )      ? ' class="'  . esc_attr( $item->class      ) .'"' : '';
		
		$item_output = '';
		
		/* Add title and normal link content - skip altogether if nolink and notext are both checked */
		if( !empty( $item->title ) && trim( $item->title ) != '' ){
			
			//Determine the title
			$title = apply_filters( 'the_title', $item->title, $item->ID );
			
			if(!empty($args->before)){
				$item_output = $args->before;
			}
			if(!in_array("header",$classes)){
				$item_output.= '<a'. $attributes .'>';
			}
			
			
			$opt_icon = $this->getMashMenuOption( $item->ID, 'icon' );
			$opt_iconPos = $this->getMashMenuOption( $item->ID, 'iconPos' );
			$opt_caretDownPos = $this->getMashMenuOption( $item->ID, 'caretDownPos' );
			
			
			
			if($depth == 0 && $opt_caretDownPos == 'left'){
				if($options['icon_mainmenu_parent'] != ''){
					$item_output .= "<i class='fa " . $options['icon_mainmenu_parent'] . "'></i>";
				} else {
					$item_output .= "<i class='fa fa-caret-down'></i>";
				}
			}
			if(!empty( $args->link_before)){
				$item_output.= $args->link_before;
			}
			
			//Text - Title
			$prepend='';
			$append='';
			$item_output.= $prepend . $title . $append;
			
			//Description
			$description ='';
			$item_output.= $description;
			
			//Link After
			if(!empty($args->link_after)){ 
				$item_output.= $args->link_after;
			}
			
			if(!in_array("header",$classes)){
				$item_output.= '</a>';
			}
			
			//Append after Link
			if(!empty($args->after)){
				$item_output .= $args->after;
			}
		}
		$with_child ='';
		if (in_array("parent", $classes)){
			$with_child ='parent';	
		}
		if($depth == 1 && isset($args->parentMega) && $args->parentMega == 'columns'){
			$sidebar = $this->getMashMenuOption( $item->ID, 'addSidebar' );
			if($sidebar != '0'){
				ob_start();
				dynamic_sidebar($sidebar);
				$html = ob_get_contents();
				ob_end_clean();
				$output .= '<li><ul class="list"><li class="header">' . $item->title . '</li><li class="cactus-widgets">'. $html .'</li></ul>';
			} else {				
				$output .= '';
			}
		} else {
			if((!isset($args->parentMega) || $args->parentMega == 'list') && $depth == 1){
				$output .= apply_filters( 'walker_nav_menu_start_el', '<li class="menu-item level'.($depth+1).' '.$with_child.''.$class_names.'">'.$item_output, $item, $depth, $args );
			} else 
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	function end_el(&$output, $item, $depth = 0, $args = array()) {
		$output .= "</li>";
	}
}

class MashMenuWalkerEdit extends Walker_Nav_Menu  {
	
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl(&$output, $depth = 0, $args = array()) {}

	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl(&$output, $depth = 0, $args = array()) {
	}
	
	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 * @since 3.0.0
	 *
	 * @global int $_wp_nav_menu_max_depth
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 * @param int    $id     Not used.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = get_the_title( $original_object->ID );
		} elseif ( 'post_type_archive' == $item->type ) {
			$original_object = get_post_type_object( $item->object );
			if ( $original_object ) {
				$original_title = $original_object->labels->archives;
			}
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)','videopro' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)','videopro'), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth )
			$submenu_text = 'style="display: none;"';

		?>
		<li id="menu-item-<?php echo esc_attr($item_id); ?>" class="<?php echo implode(' ', $classes ); ?>">
			<div class="menu-item-bar">
				<div class="menu-item-handle">
					<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo esc_attr($submenu_text); ?>><?php _e( 'sub item','videopro' ); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-up" aria-label="<?php esc_attr_e( 'Move up','videopro' ) ?>">&#8593;</a>
							|
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-down" aria-label="<?php esc_attr_e( 'Move down','videopro' ) ?>">&#8595;</a>
						</span>
						<a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>" aria-label="<?php esc_attr_e( 'Edit menu item','videopro' ); ?>"><?php esc_html_e( 'Edit','videopro' ); ?></a>
					</span>
				</div>
			</div>

			<div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
				<?php if ( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo esc_attr($item_id); ?>">
							<?php esc_html_e( 'URL','videopro' ); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-wide">
					<label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
						<?php esc_html_e( 'Navigation Label','videopro' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="field-title-attribute field-attr-title description description-wide">
					<label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
						<?php esc_html_e( 'Title Attribute','videopro' ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php esc_html_e( 'Open link in a new tab','videopro' ); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
						<?php esc_html_e( 'CSS Classes (optional)','videopro' ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
						<?php esc_html_e( 'Link Relationship (XFN)','videopro' ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
						<?php esc_html_e( 'Description','videopro' ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php esc_html_e('The description will be displayed in the menu if the current theme supports it.','videopro'); ?></span>
					</label>
				</p>

				<p class="field-move hide-if-no-js description description-wide">
					<label>
						<span><?php esc_html_e( 'Move','videopro' ); ?></span>
						<a href="#" class="menus-move menus-move-up" data-dir="up"><?php esc_html_e( 'Up one','videopro' ); ?></a>
						<a href="#" class="menus-move menus-move-down" data-dir="down"><?php esc_html_e( 'Down one','videopro' ); ?></a>
						<a href="#" class="menus-move menus-move-left" data-dir="left"></a>
						<a href="#" class="menus-move menus-move-right" data-dir="right"></a>
						<a href="#" class="menus-move menus-move-top" data-dir="top"><?php esc_html_e( 'To the top','videopro' ); ?></a>
					</label>
				</p>
				
				<?php do_action( 'mashmenu_menu_item_options', $item_id, $item, $depth, $args );?>
				
				<?php do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args ); ?>

				<div class="menu-item-actions description-wide submitbox">
					<?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s','videopro'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							admin_url( 'nav-menus.php' )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php esc_html_e( 'Remove','videopro' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
						?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Cancel','videopro'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}