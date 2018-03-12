<?php
header('content-type:text/html;charset=utf-8');
require_once 'functions/mysql.func.php';
require_once 'config/config.php';
require_once 'swiftmailer-master/lib/swift_required.php';
require_once 'functions/common.func.php';
//接受信息
$act=$_REQUEST['act'];
$username=$_REQUEST['username'];
$password=md5($_POST['password']);
$link=connect3();
$table='51zxw_user';
//根据用户不同操作完成不同功能
switch($act){
	case 'reg':
// 		echo '完成注册功能';
	//关闭自动提交
	mysqli_autocommit($link, FALSE);
	$email=$_POST['email'];
	$regTime=time();
	$token=md5($username.$password.$regTime);//生成token
	$token_exptime=$regTime+24*3600;//token过期时间
	$data=compact('username','password','email','regTime','token','token_exptime');
	$res=insert($link, $data, $table);

	//发送激活邮件
	//初始化邮件服务器对象
	$transport=Swift_SmtpTransport::newInstance('smtp.sina.com',25);
	//设置用户名和密码
	$transport->setUsername('clivelyn@sina.com');
	$transport->setPassword('lin123');
	$mailer=Swift_Mailer::newInstance($transport);//发送邮件对象
	$message=Swift_Message::newInstance();//邮件信息对象
	$message->setFrom(array('clivelyn@sina.com'));//谁发送的
	$message->setTo($email);//发送给谁
	$message->setSubject('注册账号激活邮件');//设置邮件主题

	$activeStr="?act=active&username={$username}&token={$token}";
	$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].
	$activeStr;
// 	echo $url;
// 	echo $url.urlencode($activeStr);
	$urlEncode=urlencode($url);
	//http://localhost/test/PHPAdvance/MySQLi/application/doAction.php?act=active&username=king&token=74bccca6db02607e7dd75f088ee6fee8
	$emailBody=<<<EOF
	欢迎{$username}使用账号激活功能
	请点击链接激活账号：
	<a href='{$url}' target='_blank'>{$urlEncode}</a> <br />
	(该链接在24小时内有效)
	如果上面不是链接形式，请将地址复制到您的浏览器(例如IE)的地址栏再访问。
EOF;
	$message->setBody($emailBody,"text/html",'utf-8');
	try{
		$res1=$mailer->send($message);
		var_dump($res);
		if($res && $res1){
			mysqli_commit($link);
			mysqli_autocommit($link, TRUE);
			alertMes('注册成功，立即激活使用', 'index.php');
		}else{
			mysqli_rollback($link);
			alertMes('注册失败，重新注册','index.php');
		}

	}catch(Swift_ConnectionException $e){
	    echo '123';
		die('邮件服务器错误:').$e->getMessage();
	}
	break;
	case 'active':
// 		echo '激活成功';
		$token=$_GET['token'];
		$username=mysqli_real_escape_string($link, $username);
		$query="SELECT id,token_exptime FROM {$table} WHERE username='{$username}'";
		$user=fetchOne($link, $query);
		if($user){
			//需要检测是否超时
			$now=time();
			$token_exptime=$user['token_exptime'];
			if($now>$token_exptime){
				delete($link, $table,"username='{$username}'");
				alertMes('激活码过期，请重新注册','index.php');
			}else{
				//实现激活操作
				$data=array('status'=>1);
				$res=update($link, $data, $table,"username='{$username}'");
				if($res){
					alertMes('激活成功，立即登陆','index.php');
				}else{
					alertMes('激活失败，重新激活','index.php');
				}
			}
		}else{
			alertMes('激活失败，没有找到要激活的用户！！！', 'index.php');
		}

		break;
		//检测用户是否在
	case 'checkUser':
		$username=mysqli_real_escape_string($link, $username);
		$query="SELECT id FROM {$table} WHERE username='{$username}'";
		$row=fetchOne($link, $query);
		if($row){
			echo 1;
		}else{
			echo 0;
		}
		break;
	case 'login':
		$username=addslashes($username);
		$query="SELECT id,status FROM {$table} WHERE username='{$username}' AND password='{$password}'";
		$row=fetchOne($link, $query);
		if($row){
		    alertMes('登陆成功，跳转到首页','student/layout-index.php');
// 			if($row['status']==0){
// 				alertMes('请先到邮箱激活再来登陆','index.php');
// 			}else{
// 				alertMes('登陆成功，跳转到首页','student/layout-index.php');
// 			}
		}else{
			alertMes('用户名或密码错误，重新登陆','index.php');
		}
		break;
	default:
		die('非法操作');
		break;
}