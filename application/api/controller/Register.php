<?php
//注册模块
namespace app\api\controller;

use think\Controller;
use app\api\controller\Base;

class Register extends Base 
{
	//注册方法
	public function index() {
		$request = request();
		//POST过滤
		if ($this->filterPostRequest($request)) {
			
		}
	}
}
?>