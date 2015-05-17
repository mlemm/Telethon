<?php
/*
Template Name: summaryTemplate
*/

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package _tk
 */

get_header(); ?>


	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'content', 'page' ); ?> 

		<script type="text/javascript" charset="utf-8">
			
				$(function () {
        			$("#thisbutton").click(function () { $("#thetext").html("meow meow"); }); 
        			$("#themsbutton").click(function () { 
        				$.ajax({
        url:"something.php", //the page containing php script
        type: "POST", //request type
        success:function(result){
         alert(result);
       }
     });
        			 });
        		});

    			function runPHP() {

    					alert("HGello");
    			
    				}



		</script>

		<?php
			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() )
				comments_template();
		?>

	<?php endwhile; // end of the loop. ?>


<?php get_footer(); ?>
