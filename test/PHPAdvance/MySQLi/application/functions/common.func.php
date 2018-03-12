<?php
/**
 * 弹出提示信息并且跳转
 * @param string $mes
 * @param string $url
 */
function alertMes($mes,$url){
	echo "<script>
			alert('{$mes}');
			location.href='{$url}';
	</script>";
	die;
}