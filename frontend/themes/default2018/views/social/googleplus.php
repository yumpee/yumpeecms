<?php
	if($style == 'vertical')
		$data_type = 'vertical-bubble';
	else
		$data_type = 'bubble';
?>

<div class="g-plus" data-href="<?php echo Yii::$app->request->get('link'); ?>" data-action="share" data-annotation="<?=$data_type?>"></div>

<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
