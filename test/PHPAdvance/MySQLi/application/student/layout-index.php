<?php
require_once '../functions/mysql.func.php';
require_once '../config/config.php';
header("content-type:text/html;charset=utf-8");
$link = connect3();
$page = $_GET['page']?$_GET['page']:1;
$pageSize = 5;
$offSet = ($page-1)*5;
$table = "student";
$totalRows = getTotalRows($link, $table);
$sumPage = ceil($totalRows/$pageSize);
$query = "select * from student limit {$offSet},{$pageSize}";
$rows = fetchAll($link, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>index</title>
	<link rel="stylesheet" href="static/css/bootstrap.min.css">
	<script text="text/javascript" src="static/js/jquery-3.1.0.min.js"></script>
	<style type="text/css">
		body{ font-family: 'Microsoft YaHei';}
		/*.panel-body{ padding: 0; }*/
		span{
			 color: blue;
		}
	</style>
</head>
<body>
<div class="jumbotron">
	<div class="container">
		<h1>51学生管理系统 V1.0</h1>


	</div>
</div>
<div class="container">
	<div class="main">
		<div class="row">
			<!-- 左侧内容 -->
			<div class="col-md-3">
				<div class="list-group">
					<a href="" class="list-group-item text-center active">查看学生</a>
					<a href="layout-form.html" class="list-group-item text-center">新增学生</a>

				</div>
			</div>
			<!-- 右侧内容 -->
			<div class="col-md-9">
				<!-- 成功提示框 -->
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<strong>成功！</strong> 操作成功提示
				</div>
				<!-- 失败提示框 -->
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<strong>失败！</strong> 操作失败提示
				</div>
				<!-- 自定义内容 -->
				<div class="panel panel-default">
					<div class="panel-heading">学生列表</div>
					<div class="panel-body">
						<table class="table table-striped table-responsive table-hover">
							<thead>
								<tr>
									<th>id</th>
									<th>姓名</th>
									<th>年龄</th>
									<th>性别</th>
									<th width="120">操作</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($rows as $admin):?>
								<tr>
									<th><?php echo $admin['id']?></th>
									<th><?php echo $admin['username']?></th>
									<th><?php echo $admin['age']?></th>
									<th><?php echo $admin['sex']?></th>
									<td>
									   <span>详情</span>
									   <span>修改</span>
									   <span class="delect" data_id="<?php echo $admin['id']?>">删除</span>
									</td>
								</tr>
							<?php endforeach;?>
							</tbody>
							<script>
							  $(function(){
								    $(".delect").click(function(){
								        var id = $(this).attr("data_id");
								        var url = "doAction.php?id="+id;
								        $.get(url);
								        $(this).parent().parent().empty();
										    });
								  });
							</script>
						</table>
					</div>
				</div>

				<nav>
					<ul class="pagination pull-right">
					<?php
					   for($i = 1;$i<=$sumPage;$i++){
					       echo "<li><a href='layout-index.php?page={$i}'>$i</a></li>";
					   }

					?>
					</ul>
				</nav>
			</div>
		</div>
  	</div>
</div>
<!-- 尾部 -->
<div class="jumbotron" style=" margin-bottom:0;margin-top:105px;">
	<div class="container">
	<span>&copy; 2016 Saitmob</span>
	</div>
</div>


	<script src="static/js/jquery-3.1.0.min.js"></script>
	<script src="static/js/bootstrap.min.js"></script>
</body>
</html>