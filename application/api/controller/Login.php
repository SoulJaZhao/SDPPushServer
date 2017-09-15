<?php
//登录模块
namespace app\api\controller;

use think\Controller;
use app\api\controller\Base;

class Login extends Base 
{
	//登录方法
	public function index() {
		$result = [
			"errorCode"		=>		0,
			"errorMsg"		=>		"",
			"data"			=>		"hahah"
		];
		echo json_encode($result);
	}
}
?>