<?php
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
session_start();
$_SESSION['chartData']=array('dataArr'=>array(4,5,8,2),'dataIndex'=>array("项1","项2","项3","项3"),'color'=>array(255,0,0),'xWidth'=>50,'yWidth'=>40);
?>
<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>php chart</title>
   <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
   <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
   <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
</head>
<body>

<?='<img src="bar_Chart.php?bar_Chart=chartData" /><br/>';?>

<?='<img src="line_Chart.php?line_Chart=chartData" /><br/>';?>
</body>
</html>