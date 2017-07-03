<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package cactus
 */
?>

<section class="no-results not-found">
	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( esc_html__( 'Sẵn sàng đăng bài đầu tiên của bạn? ', 'videopro' ).'<a href="%1$s">'.esc_html__('Get started here','videopro').'</a>.', esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Xin lỗi. Chúng tôi không tìm thấy kết quả phù hợp với từ khóa của bạn. Xin vui lòng tìm theo từ khóa khác.', 'videopro' ); ?></p>

		<?php else : ?>

			<p><?php esc_html_e( 'Chúng tôi không tìm thấy cái bạn đang tìm kiếm. Xin vui lòng thử chức năng tìm kiềm.', 'videopro' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
