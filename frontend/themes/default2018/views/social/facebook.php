<?php
	if($style == 'vertical')
		$data_type = 'box_count';
	else
		$data_type = 'button_count';
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-share-button" data-href="<?php echo Yii::$app->request->get('link'); ?>" data-width="200" data-type="<?=$data_type?>" style="vertical-align: top;"></div>
