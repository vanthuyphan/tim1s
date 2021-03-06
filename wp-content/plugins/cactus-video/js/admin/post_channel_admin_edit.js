(function($) {

	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			var $post_row = $( '#post-' + $post_id );
			
			if($($('input.post_format', $post_row)[0]).val() != 'video'){
				// hide the box if it is not video post
				$('.post-channels-edit', $edit_row).hide();
			} else {
				// get the data
				var vals = $(':input[name="post_channels"]', $post_row );
				if(vals.length > 0){
					$.each(vals, function(index, value){
						var channel_id = $(value).val();
						$('#in-channel-' + channel_id).prop('checked', true);
					});
				}
			}
		}
	};
	
	$( document ).on( 'click', '#bulk_edit', function() {
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );

		// get the selected post ids that are being edited
		var $post_ids = new Array();
		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});

		// get the data
		var $selected_channels = $bulk_row.find( 'input[name="post_channel[]"]:checked' );
		var $channel_ids = [];
		$.each($selected_channels, function(index, value){
			$channel_ids.push($(value).val());
		});
		

		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'videopro_save_bulk_edit_post_channels', // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids,
				channels: $channel_ids
			}
		});
	});
})(jQuery);