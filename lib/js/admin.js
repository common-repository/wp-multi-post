var count = 1;
function expandpbox()
{
	document.getElementById("test"+count).style.display = 'block';
	var count2 = count + 1 ;
	var ifrmID = 'wpmpeditor'+count2+'_ifr';
	document.getElementById(ifrmID).style.height = '300px' ;
	count++;
	if( count == 15 )
	{
		document.getElementById("expandBox").style.display = 'none' ;
	}
}

jQuery(document).ready(function()
{
	jQuery(".wpmp_post_tag_icon").on("click", function()
	{
		jQuery(".wpmp_post_tag .wpmp_post_tag_box").toggle();
		jQuery(".wpmp_post_tag .dashicons-arrow-down-alt2").toggle();
		jQuery(".wpmp_post_tag .dashicons-no-alt").toggle();
	});
});

jQuery(document).ready(function()
{
	jQuery(".wpmp_post_category_icon").on("click", function()
	{
		jQuery(".wpmp_post_category .wpmp_post_category_box").toggle();
		jQuery(".wpmp_post_category .dashicons-arrow-down-alt2").toggle();
		jQuery(".wpmp_post_category .dashicons-no-alt").toggle();
	});
});
