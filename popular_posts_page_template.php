<?php
/**
	Template Name: Page with background only

*/
require 'gapi.class.php';
get_header();


function generateReport($period)
{
	/* ============ Get Most Popular Posts  ============ */
	// get GA info from post meta data
	try{
		$ga_account = get_post_meta( get_the_ID(),'userId',true) ;
		$ga_password=get_post_meta( get_the_ID(),'userPassword',true);
		$ga_profile_id=get_post_meta( get_the_ID(),'userProfileId',true);
		 
		$ga = new gapi($ga_account,$ga_password);
		$dimensions = array('pagePath');
		$metrics = array('uniquePageviews');
		$sort = '-uniquePageviews';
		$fromDate = date('Y-m-d', strtotime($period));

		$toDate = date('Y-m-d');

		$mostPopular = $ga->requestReportData($ga_profile_id, $dimensions, $metrics, $sort, null, $fromDate, $toDate);
		/* ============ Get Most Popular Posts  ============ */
		
		/*============= Form The Report ===========*/
		$counter=0;
		$uniquePosts=array(get_the_ID()); // just to make sure there is no duplicated posts
		foreach($mostPopular as $item){
			$postid=url_to_postid($item);
			if($postid!=0 && !in_array($postid,$uniquePosts))
			{
				array_push($uniquePosts,$postid);
				$page_data = get_page( $postid);
				if($page_data!=null )
				{
					echo '<br/>';
					echo '<li>';
					echo '<h><a href='.$item.'>'.$page_data->post_title.'</a></h>';
					echo '</li>';
					$counter++;
				}
			}
			if($counter==10) break;
		}
		/*============= Form The Report ===========*/
	}
	catch(Exception $e){
		echo "Error Connecting Google Analytics , Try Again Later " ;
	}
}
?>
<!--============== Some Code To Change The Report Scope ( Week , Month , Year ) ==================== -->
<script>
function changeTab(id)
{
     var ulW=document.getElementById('ulW');
	 ulW.style.display = "none";
	 
	 var ulM=document.getElementById('ulM');
	 ulM.style.display = "none";
	 
	 var ulY=document.getElementById('ulY');
	 ulY.style.display = "none";
	 
	var current = document.getElementById(id);
	current.style.display = "block";
}
</script>
<!--============== Some Code To Change The Report Scope ( Week , Month , Year ) ==================== -->

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div id="popularPosts" style="margin: 50px">
				<input type="button" id='btnW' name='btnW' value="Week"  onclick="return changeTab('ulW');"/>
				<input type="button" id='btnM' name='btnM' value="Month" onclick="return changeTab('ulM');"/>
				<input type="button" id='btnY' name='btnY' value="Year"  onclick="return changeTab('ulY');"/>
				<hr/>
				
				<div id="ulW" name="ulW" >
					<h3>The Most Popular Posts For The Last Week Are : </h3>
					<ul><?php generateReport('-7 days');?></ul>
				</div>
								
				<div id="ulM" name="ulM" style="display:none" value="test">
					<h3>The Most Popular Posts For The Last Month Are : </h3>
					<ul><?php generateReport('-30 days');?></ul>
				</div>
				
				<div id="ulY" name="ulY" style="display:none">
					<h3>The Most Popular Posts For The Last Year Are : </h3>
					<ul><?php generateReport('-365 days');?></ul>
				</div>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();