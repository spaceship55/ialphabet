<?php
 
function vubon_add_amaz_img_icons(){
?>
 
<style>
#adminmenu .menu-icon-amaz-hover div.wp-menu-image:before {
content: "\f180";
}
</style>
 
<?php
}
add_action( 'admin_head', 'vubon_add_amaz_img_icons' );
?>