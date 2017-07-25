<?php
defined('ABSPATH') or die();
?>
<div class="paging-nav col-xs-12" dir="ltr">
<?php
$big = 999999999; // need an unlikely integer
	$pagination=paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'type'=>'array',
		'prev_text'=>'<span></span>',
		'next_text'=>'<span></span>'
	) );

if(is_array($pagination)){
	print join("\n",is_rtl()?array_reverse($pagination):$pagination);
}
?>
</div>
