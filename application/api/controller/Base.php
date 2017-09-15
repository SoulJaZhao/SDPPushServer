<?php
//公共组件
namespace app\api\controller;

use think\Controller;
use think\Validate;

class Base extends Controller 
{
	//创建返回
	final protected function createErrorResponse($errorCode,$errorMsg) {
		$responseArray = [
			"errorCode"		=>		$errorCode,
			"errorMsg"		=>		$errorMsg,
			"data"		=>		''
		];
		return json_encode($responseArray);
	}

	//成功返回数据
    final protected function createSuccessResponse($data) {
        $responseArray = [
            "errorCode"		=>		0,
            "errorMsg"		=>		'',
            "data"		=>		$data
        ];
        return json_encode($responseArray);
    }

	/* 过滤POST请求
	 * @param request 请求对象 
	 * @param rules 过滤规则
	 */
	protected function filterPostRequest($request, $rules) {
		//请求方法
		$method = $request->method();
		//请求方式错误
		if (strtolower($method) != 'post') {
			echo $this->createErrorResponse('100','请求方式错误');
			return false;
		} else {
			//过滤参数
			$validate = new Validate($rules);

			$result = $validate->check($request->param());
			if (!$result) {
				echo $this->createErrorResponse('101','缺少参数');
				return false;
			} else {
				return true;
			}
		}
	}
}
?>