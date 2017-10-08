<?php
// Added Shortcode with ID
add_filter( 'manage_rd-hover_posts_columns', 'rd_hover_add_id_column' );
add_action( 'manage_rd-hover_posts_custom_column', 'rd_hover_id_column_content', 10, 2 );

function rd_hover_add_id_column( $columns ) {
	$columns['rd-hover'] = 'Hover Shortcode';

	return $columns;
}

function rd_hover_id_column_content( $column, $id ) {
	if ( 'rd-hover' == $column ) {
		$shortcode_render = '[hover id="' . $id . '"]';

		echo '<input style="min-width:210px" type=\'text\' onClick=\'this.setSelectionRange(0, this.value.length)\' value=\'' . $shortcode_render . '\' />';
	}
}

// Gallery custom messages
add_filter( 'post_updated_messages', 'rd_hover_updated_messages' );
function rd_hover_updated_messages( $messages ) {

	global $post;
	$post_ID = get_the_ID();

	$messages['rd-hover'] = array(
		0 => '',
		1 => sprintf( __( 'Hover Effects published. Shortcode is: %s' ), '' . '
<script type="text/javascript">
(function() {
    "use strict";
  // click events
  document.body.addEventListener("click", copy, true);

    // event handler
    function copy(e) {

    // find target element
    var 
      t = e.target,
      c = t.dataset.copytarget,
      inp = (c ? document.querySelector(c) : null);
      
    // is element selectable?
    if (inp && inp.select) {
      
      // select text
      inp.select();

      try {
        // copy text
        document.execCommand("copy");
        inp.blur();
        
        // copied animation
        t.classList.add("copied");
        setTimeout(function() { t.classList.remove("copied"); }, 1500);
      }
      catch (err) {
        alert("please press Ctrl/Cmd+C to copy");
      }    
    } 
    }

})();	
</script>
	
			' . '<input style="height: 30px; margin-right: 5px;" type="text" id="website" value="[hover id=\'' . $post_ID . '\']" /><button  class="button button-primary button-large" data-copytarget="#website">copy</button>' ),
		6 => sprintf( __( 'Hover Effects published. Shortcode is: %s' ), '' . '  ' . '
<script type="text/javascript">
(function() {
    "use strict";
  // click events
  document.body.addEventListener("click", copy, true);

    // event handler
    function copy(e) {

    // find target element
    var 
      t = e.target,
      c = t.dataset.copytarget,
      inp = (c ? document.querySelector(c) : null);
      
    // is element selectable?
    if (inp && inp.select) {
      
      // select text
      inp.select();

      try {
        // copy text
        document.execCommand("copy");
        inp.blur();
        
        // copied animation
        t.classList.add("copied");
        setTimeout(function() { t.classList.remove("copied"); }, 1500);
      }
      catch (err) {
        alert("please press Ctrl/Cmd+C to copy");
      }    
    } 
    }

})();	
</script>
<input style="height: 30px; margin-right: 5px;" type="text" id="website" value="[hover id=\'' . $post_ID . '\']" /><button  class="button button-primary button-large" data-copytarget="#website">copy</button>' ),
	);


	return $messages;

}