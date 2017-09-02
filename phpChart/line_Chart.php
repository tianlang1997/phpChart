<?php
	/*
	eg:
		$_SESSION['line_Chart']=array(
			dataArr=>array(),//显示数据，必选
			dataIndex=>array(),//坐标下标，可选
			xWidth=>number,//横坐标刻度宽，可选
			yWidth=>number,//纵坐标刻度宽，可选
			yCoordNum=>number,//Y轴刻度个数，可选
			color=>array(r,g,b),//图像颜色，可选
			fontSize=>number//字体大小
			xUnit=>String,//x轴单位
			yUnit=>String,//Y轴单位
			);
	*/
	session_start();
	$X=2000;//X轴长
	$Y=500;//Y轴长

	if($_GET['line_Chart'] && $_SESSION[$_GET['line_Chart']] && $_SESSION[$_GET['line_Chart']]["dataArr"])
	{
		drawPicture($_SESSION[$_GET['line_Chart']]);
	}
	
	function drawPicture($chartData){	
		$start_X=100;//X起点坐标
		$start_Y=100;//Y起点坐标
		$XcoordWidth=20;//X轴刻度宽
		$YcoordWidth=60;//Y轴刻度宽
		$Y_coordNum=5;//Y轴坐标数		
		$data=$chartData["dataArr"];
		$dataIndex=$chartData["dataIndex"];	
		$fontSize=8;//字体大小	
		if($dataIndex==NULL){
			$dataIndex=array();
			for($i=0;$i<count($data);$i++){
				$dataIndex[$i]=$i+1;
			}	
		}
				
		if($chartData["xWidth"]){
			$XcoordWidth=$chartData["xWidth"];			
		}
		
		if($chartData["yCoordNum"]){
			$Y_coordNum=$chartData["yCoordNum"];
		}
		
		if($chartData["yWidth"]){
			$YcoordWidth=$chartData["yWidth"];
			
		}
		
		if($chartData["fontSize"]){
			$fontSize=$chartData["fontSize"];
		}
		
		$GLOBALS['X']=$XcoordWidth*count($dataIndex)+3*$XcoordWidth;
		$GLOBALS['Y']=$YcoordWidth*$Y_coordNum+1*$YcoordWidth;	
		
		
		$imgrs= imagecreate($GLOBALS['X']+$start_X,$GLOBALS['Y']+$start_Y);//初始化画布
		imagecolorallocate($imgrs, 255, 255, 255);//画布底色
		$color = imagecolorallocate($imgrs,0,0,0);//坐标轴画笔颜色
		$penColor=imagecolorallocate($imgrs,0,0,0);//画笔颜色
		if($chartData["color"]){
			$penColor=imagecolorallocate($imgrs,$chartData["color"][0],$chartData["color"][1],$chartData["color"][2]);
		}
		
		//imagestring($imgrs,2,10,20,$colorData[0]."--------".$colorData[1]."----------".$colorData[2],$color);
		$Max=max($data);
		$Max_str=(string)$Max;
		$rankNum=pow(10,strlen($Max_str)-1);
		if($Max==$rankNum || $Max==$rankNum*5){
			$Max=$Max;
		}else if($Max>5*$rankNum){
			$Max=$rankNum*10;
		}else{
			 $Max=$rankNum*5;
		}
		$Y_value=$Max/$Y_coordNum;
		
		$font = "./font/simhei.ttf";
		//x轴单位
		if($chartData["xUnit"]){
			imagettftext($imgrs,$fontSize,0,$GLOBALS['X']+$XcoordWidth,turnX($start_Y-2*$fontSize),$color,$font,$chartData["xUnit"]);
		}
		//Y轴单位
		if($chartData["yUnit"]){
			imagettftext($imgrs,$fontSize,0,$start_X-50,$YcoordWidth,$color,$font,$chartData["yUnit"]);
		}
		//imagestring($imgrs,2,10,20,"Max=".$Max.'----Y_value='.$Y_value,$color);
		//X轴绘制
		imageline($imgrs , $start_X , turnX($start_Y) ,$GLOBALS['X']+$start_X , turnX($start_Y) , $color);
		for($i=0;$i<count($dataIndex);$i++){
			imageline($imgrs , $start_X+$i*$XcoordWidth  , turnX($start_Y) , $start_X+$i*$XcoordWidth , turnX($XcoordWidth/4+$start_Y) , $color);
			imagettftext($imgrs,$fontSize,0,$start_X+($i+1)*$XcoordWidth-5,turnX($start_Y-2*$fontSize),$color,$font,$dataIndex[$i]);	
		}
		imageline($imgrs , $start_X+count($dataIndex)*$XcoordWidth  , turnX($start_Y) , $start_X+count($dataIndex)*$XcoordWidth , turnX($XcoordWidth/4+$start_Y) , $color);
		//Y轴绘制
		imageline($imgrs , $start_X , turnX($start_Y) , $start_X , turnX($GLOBALS['Y']+$start_X), $color);		
		for($i=0;$i<$Y_coordNum;$i++){
			imageline($imgrs , $start_X  , turnX($start_Y+$i*$YcoordWidth), $XcoordWidth/4+$start_X , turnX($start_Y+$i*$YcoordWidth) , $color);
			imagettftext($imgrs,$fontSize,0,$start_X-50,turnX($start_Y+($i+1)*$YcoordWidth),$color,$font,($i+1)*$Y_value);		
		}
		imageline($imgrs , $start_X  , turnX($start_Y+$Y_coordNum*$YcoordWidth), $XcoordWidth/4+$start_X , turnX($start_Y+$Y_coordNum*$YcoordWidth) , $color);
		//零点绘制
		imagettftext($imgrs,$fontSize,0,$start_X-2*$fontSize,turnX($start_Y-2*$fontSize),$color,$font,"0");
		//数值比例
		$scaleRate=$YcoordWidth/$Y_value;
		//画图
		imagettftext($imgrs,$fontSize,0,$start_X+$XcoordWidth,turnX($start_Y+$data[0]*$scaleRate+20),$penColor,$font,$data[0]);		
		for($i=1;$i<count($data) && $i<count($dataIndex);$i++){
			imageline($imgrs , $start_X+($i*$XcoordWidth) , turnX($start_Y+$data[$i-1]*$scaleRate), $start_X+(($i+1)*$XcoordWidth) , turnX($start_Y+$data[$i]*$scaleRate) , $penColor);	
			imagettftext($imgrs,$fontSize,0,$start_X+(($i+1)*$XcoordWidth),turnX($start_Y+$data[$i]*$scaleRate+20),$penColor,$font,$data[$i]);	
		}
		
		//输出图像  
		header("Content-type: image/jpeg"); 
		//exit(); 
		imagejpeg($imgrs);
		imagedestroy($imgrs);
		//echo $_SESSION['views'];
	}
	//以$GLOBALS['Y']/2+90 为轴翻转
	function turnX($y){
		$distance=($GLOBALS['Y']/2+80)-$y;
		return $y+2*($distance);	
	}
