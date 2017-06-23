<?php
/**
 * cactus theme sample theme options file. This file is generated from Export feature in Option Tree.
 *
 * @package videopro
 */

//hook and redirect
function videopro_activation($oldname, $oldtheme=false) {
	//header( 'Location: '.admin_url().'admin.php?page=cactus-welcome');
	wp_redirect(admin_url().'themes.php?page=videopro-welcome');
}
add_action('after_switch_theme', 'videopro_activation', 10 ,  2); 

//welcome menu
add_action('admin_menu', 'videopro_welcome_menu');
function videopro_welcome_menu() {
	add_theme_page(esc_html__('Welcome','videopro'), esc_html__('VideoPro Welcome','videopro'), 'edit_theme_options', 'videopro-welcome', 'videopro_welcome_function', 'dashicons-megaphone', '2.5');
}

//welcome page
function videopro_welcome_function(){
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome/css/font-awesome.min.css', array(), '4.3.0');
    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome';
    ?>
    <div class="wrap">
        <?php
		videopro_welcome_tabs();
		videopro_welcome_tab_content( $tab );
		?>
    </div>
    <?php
}

function videopro_admin_enqueue_scripts() {
        wp_enqueue_style( 'videopro-adm-google-fonts', videopro_get_google_fonts_url(array('Poppins')), array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'videopro_admin_enqueue_scripts' );

//tabs
function videopro_welcome_tabs() {
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome';
	$cactus_welcome_tabs = array(
		'welcome' => '<span class="dashicons dashicons-smiley"></span> '.esc_html__('Welcome','videopro'),
		'document' => '<span class="dashicons dashicons-format-aside"></span> '.esc_html__('Document','videopro'),
		'sample' => '<span class="dashicons dashicons-download"></span> '.esc_html__('Sample Data','videopro'),
		'support' => '<span class="dashicons dashicons-businessman"></span> '.esc_html__('Support','videopro'),
	);
	
	echo '<h1></h1>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $cactus_welcome_tabs as $tab_key => $tab_caption ) {
        $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
        echo '<a class="nav-tab ' . $active . '" href="?page=videopro-welcome&tab=' . $tab_key . '">' . $tab_caption . '</a>';
    }
    echo '</h2>';
}
function videopro_welcome_tab_content( $tab ){
	if($tab == 'document'){ ?>
    	<p>You could view <a class="button button-primary button-large" href="http://videopro.cactusthemes.com/doc/" target="_blank">Full Document</a> in new window</p        
    ><?php 
	} elseif($tab == 'sample'){
    	if(!class_exists('cactus_demo_importer')){
			?>
			<p style="color:#FF0000"> <?php echo esc_html__('Please install VideoPro-SampleData plugin to use this feature','videopro');?> </p>
			<?php
		} else {
			do_action('videopro_import_data_tab'); 
		}
	} elseif($tab == 'support'){ ?> 
    	<p>You could open <a class="button button-primary button-large" href="http://ticket.cactusthemes.com/" target="_blank">Support Ticket</a> in new window</p>        
	<?php } else{ ?>
		<div class="cactus-welcome-message">
			<h2 class="cactus-welcome-title"><?php esc_html_e('Welcome to VideoPro - The Ultimate Video Solution for WordPress','videopro');?></h2>
            <div class="cactus-welcome-inner">
                <a class="cactus-welcome-item" href="?page=videopro-welcome&tab=document">
                	<i class="fa fa-book"></i>
                    <h3><?php echo esc_html__('Full Document','videopro'); ?></h3>
                    <p><?php echo esc_html__('See the full user guide for all VideoPro functions','videopro'); ?></p>
                </a>
                <br />
                <a class="cactus-welcome-item" href="?page=videopro-welcome&tab=sample">
                	<i class="fa fa-download"></i>
                    <h3><?php echo esc_html__('Sample Data','videopro'); ?></h3>
                    <p><?php echo esc_html__('Import sample data to have homepage like our live DEMO','videopro'); ?></p>
                </a>
                <a class="cactus-welcome-item" href="?page=videopro-welcome&tab=support">
                	<i class="fa fa-user"></i>
                    <h3><?php echo esc_html__('Support','videopro'); ?></h3>
                    <p><?php echo esc_html__('Need support using the theme? We are here for you.','videopro'); ?></p>
                </a>
                <div class="cactus-welcome-item cactus-welcome-item-wide cactus-welcome-changelog">
                	<h3>Release Logs</h3>
                        <ul>
<li><span>VideoPro 2.0.5.6 - 2017/02/06</span><br/>
includes Cactus-Video 2.0.5.7; Cactus-Ads 2.5.4.3
<ul>
	<li>#Fix: Cover image in BuddyPress profile page does not show</li>
	<li>#Fix: cannot open full-screen Facebook Video</li>
	<li>#Fix: warning errors in Membership sign up page</li>
	<li>#Fix: Video Ads doesn't show up when Video Header option is Thumbnail Image</li>
	<li>#Fix: HTML iframe Ads doesn't show up (should not use a Video Iframe in Video Ads)</li>
	<li>#Fix: option to show Post Likes button to be separated from Social Sharing option</li>
	<li>#Fix: cannot change Video Player Background Color</li>
	<li>#Add: option to show BuddyPress notification bubble on header (in Theme Options > BuddyPress)</li>
</ul>
</li>						
<li><span>VideoPro 2.0.5.5 - 2017/02/06</span><br/>
includes Cactus-Video 2.0.5.6; VideoPro-Shortcodes 1.2.0.5;
<ul>
	<li>#Fix: unable to open Lightbox Player on mobile</li>
	<li>#Fix: sidebar setting for Shop page</li>
	<li>#Fix: using incorrect self-hosted video player for Posts Slider with inline player</li>
	<li>#Fix: Channels and Playlists does not list all in video Submit Form</li>
	<li>#Fix: css issues</li>
	<li>#Update: language file</li>
</ul>
</li>
<li><span>VideoPro 2.0.5.4 - 2017/01/25</span><br/>
includes Cactus-Video 2.0.5.5; VideoPro-Shortcodes 1.2.0.4;
<ul>
    <li>#Fix: Layout Switch Tool in category page doesn't work properly</li>
    <li>#Fix: draft posts appear in Category page</li>
    <li>#Fix: JW Player 7 issues with Posts Slider shortcode using inline and lightbox player mode</li>
    <li>#Update: support Autoplay feature of JW Player 7 Premium version</li>
    <li>#Update: add Gallery and Audio icon for appropriate post formats</li>
</ul>
</li>
                        <li><span>VideoPro 2.0.5.3 - 2016/12/29</span><br/>
includes Cactus-Video 2.0.5.4; Cactus-Actor 1.0.4.5
<ul>
    <li>#Update: only admin can mark an user "verified"</li>
    <li>#Update: support Video Resolution toggle for JW Player 7</li>
    <li>#Fix: missing "Search Results for..." in breadcrumbs</li>
    <li>#Fix: translate text in admin of Cactus Actor plugin</li>
    <li>#Fix: Ajax loading issue in single Playlist</li>
</ul>
</li>
                        <li><span>VideoPro 2.0.5.2 - 2016/12/24</span><br/>
includes Cactus-Video 2.0.5.3; Advance-Search-Form 1.4.9.6; VideoPro-SampleData 1.3
<ul>
    <li>#Fix: issues with Force Using JWPlayer for YouTube videos</li>    
    <li>#Fix: incorrect Video Duration when using YouTube WP Plugin to import videos</li>
    <li>#Fix: require choosing Categories in Contact Form 7 - Submission form</li>
    <li>#Fix: incorrect next/prev videos when time format is not standard</li>
    <li>#Update: sample data for V2</li>
    <li>#Update: lazy load background image</li>
    <li>#Update: sanitize search input value to security</li>
    <li>#Update: show Like/Dislike buttons for standard posts</li>
</ul>
</li>
                            <li><span>VideoPro 2.0.5.1 - 2016/12/15</span><br/>
includes Cactus-Video 2.0.5.2;
<ul>
    <li>#Fix: link to comments section</li>
    <li>#Fix: conflict with Captcha field in Post Submission form</li>
    <li>#Fix: bulk edit - adding videos to series</li>
    <li>#Fix: ajax pagination in Channels Listing page - WP 4.7 issue</li>
    <li>#Update: support Contact Form 4.6</li>
    <li>#Add: option to set height for popup form</li>
    <li>#Add: option to close popup form when clicking outside content</li>
</ul>
</li>
<li><span>VideoPro 2.0.5 - 2016/12/09</span><br/>
includes Cactus-Video 2.0.5; Cactus-Actor 1.0.4.3
<ul>
    <li>#Fix: Carousel Slider in RTL on IE browser</li>
    <li>#Fix: auto-next feature for YouTube videos</li>
    <li>#Fix: various issues</li>    
    <li>#Fix: cannot close Floating Video if Video Header is Thumbnail</li>
    <li>#Fix: RTL issues</li>
    <li>#Update: correct comments link when Disqus is used</li>
    <li>#Update: show views, likes, comments count in Popular Posts Widget based on condition</li>
    <li>#Update: WordPress 4.7 support</li>
    <li>#Add: Channel Thumbnail option, to upload Channel Thumbnail</li>
    <li>#Add: option to print out video meta tags, support Google Search Results</li>
    <li>#Add: option to mark Channel, Actor and Author "verified"</li>
</ul>
</li>
<li><span>VideoPro 2.0.4 - 2016/12/06</span><br/>
includes Cactus-Video 2.0.4;
<ul>
    <li>#Fix: adding videos to series using Quick Edit doesn't work</li>
    <li>#Fix: Twitter meta tags</li>
    <li>#Fix: drag and drop file in Front-end Post Submission, using GravityForms</li>
    <li>#Fix: do not refresh after uploading videos</li>
    <li>#Update: support Category, Playlist, Channel dropdown in GravityForms</li>
    <li>#Update: order of videos in Actor and Playlist page changes to DESC</li>
    <li>#Update: support deLucks SEO plugin</li>
    <li>#Update: languge file</li>
</ul>
</li>
<li><span>VideoPro 2.0.3 - 2016/11/30</span><br/>
includes Cactus-Video 2.0.3; VideoPro-Shortcodes 1.2.0.3; Visual Composer 5.0.1
<ul>
    <li>#Fix: option to hide view count doesn't work in standard posts</li>
    <li>#Fix: param Column of Authors Listing shortcode doesn't work</li>
    <li>#Fix: Ajax pagination doesn' work in Channel and Author pages</li>
    <li>#Fix: "Agreement" checkbox in Creating Channel form doesn't require</li>
    <li>#Update: Option to turn on/off Public Profile menu item</li>
    <li>#Update: Option to add Register menu item</li>
    <li>#Update: language files</li>
</ul>
</li>
                            <li><span>VideoPro 2.0.2 - 2016/11/21</span><br/>
                                includes Cactus-Video 2.0.2; VideoPro-Shortcodes 1.2.0.2; Cactus-Ads 2.5.4.1; Advance Search Form 1.4.9.4
                                <ul>
                                    <li>#Fix: incorrect Back link when going to Edit Video page from Public Profile</li>
                                    <li>#Fix: Tag suggestion of Advance Search Form</li>
                                    <li>#Fix: option to hide comments count in Single Video page</li>
                                    <li>#Fix: when creating first playlist in channel, it does not appear</li>    
                                    <li>#Fix: missing Edit Playlist button</li>
                                    <li>#Fix: https issue with Google Reponsive Ad shortcode</li>
                                    <li>#Update: add Delete link in Edit Popup of Channel and Playlist for quick action</li>
                                    <li>#Update: improve RTL and language files</li>
                                    <li>#Update: able to select Ads from dropdown</li>
                                    <li>#Update: option to change Categories, Channels, Playlists checkbox and radio group into Dropdown</li>
                                    <li>#Update: add option to Force Using JWPlayer for YouTube videos</li>
                                    <li>#Update: add filter to change number of videos in single actor page (http://bit.ly/2g9Ojqt)</li>
                                    <li>#Update: support hierarchical layout in Categories widget</li>
                                    <li>#Update: add option to set default status for User-Generated Content in Video Extensions > Membership Settings</li>
                                </ul>
                            </li>
                            <li><span>VideoPro 2.0.1 - 2016/11/11</span><br/>
                            includes Cactus-Video 2.0.1; VideoPro-Shortcodes 1.2.0.1
                            <ul>
                                <li>#Fix: checking incorrect post format in SCB shortcodes</li>
                                <li>#Fix: set default value for Delay Before Auto-Next</li>
                                <li>#Update: option to disable Video Ads for a specific Membership</li>
                                <li>#Update: support entering Video Duration for self-hosted videos</li>
                                <li>#Update: Visual Composer 5.0</li>
                                <li>#Update: language files of plugins</li>
                            </ul>
                            </li>
                            <li><span>VideoPro 2.0 - 2016/11/07</span></br/>
                            includes Cactus-Videos 2.0;  VideoPro-Shortcodes 1.2; Cactus-Actors 1.1;   
                            <ul>
                            <li>#Fix: issues with Video Duration</li>
                            <li>#Fix: Watched Later template</li>
                            <li>#Fix: various bugs</li>
                            <li>#Update: adding Twitter Metadata</li>
                            <li>#Update: adding Customg Social Accounts for Actor</li>
                            <li>#Update: support Short YouTube URL (youtu.be)</li>
                            <li>#Update: adding Random condition for SCB shortcode</li>
                            <li>#Add: Membership features</li>
                            <li>#Add: support WPMU Membership 2 plugin</li>
                            <li>#Add: support BuddyPress</li>
                            </ul>
                            </li>
                            <li>Full release logs: <a href="http://videopro.cactusthemes.com/release_log.html" target="_blank">Release Logs</a></li>
                        </ul>
                </div>
            </div>
		</div>
	<?php }
}


//old import sample data
add_action( 'admin_notices', 'videopro_print_current_version_msg' );
function videopro_print_current_version_msg()
{
	$current_theme = wp_get_theme('videopro');
	$current_version =  $current_theme->get('Version');
    
    // check child theme version
    $child_theme = wp_get_theme();
    if($child_theme->get('Name') != $current_theme->get('Name')){
        $current_version .= '. ' . $child_theme->get('Name') . ' ver.' . $child_theme->get('Version');
    }
	echo '<div style="display:none" id="current_version">' . $current_version . '</div>';
}

add_action( 'admin_footer', 'videopro_import_sample_data_comfirm' );
function videopro_import_sample_data_comfirm()
{
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#ct_support_forum').parent().attr('target','_blank');
        $('#ct_documentaion').parent().attr('target','_blank');
        $('#option-tree-sub-header').append('<span class="option-tree-ui-button left text">VideoPro</span><span class="option-tree-ui-button left vesion ">ver. ' + $('#current_version').text() + '</span>');
    });
    </script>
    <?php
}