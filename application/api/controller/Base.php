<?php
//公共组件
namespace app\api\controller;

use think\Controller;

class Base extends Controller 
{
	//创建返回
	final protected function createResponse($errorCode,$errorMsg,$data) {
		$responseArray = [
			"errorCode"		=>		$errorCode,
			"errorMsg"		=>		$errorMsg,
			"data"		=>		$data
		];
		return json_encode($responseArray);
	}

	// 过滤POST请求
	protected function filterPostRequest($request) {
		//请求方法
		$method = $request->method();
		//请求方式错误
		if (strtolower($method) != 'post') {
			echo $this->createResponse('100','请求方式错误','');
			return false;
		} else {
			return true;
		}
	}
}
?>