<?php
//公共组件
namespace app\api\controller;

use think\Controller;
use think\Validate;
use think\Session;

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

    /* 过滤在线状态请求
     * @param request 请求对象
     * @param rules 过滤规则
     */
    protected function filterOnlineStatusRequest($request, $rules) {
        //请求方法
        $method = $request->method();

        // 判断是否缺少AccessToken参数
        $accessToken = trim($request->param('accessToken'));

        if (is_null($accessToken)) {
            echo $this->createErrorResponse('101','缺少参数');
            return false;
        } else {
            // 判断会话是否超时
            if ($accessToken != Session::get('accessToken')) {
                echo $this->createErrorResponse('102','登陆超时');
                return false;
            }
        }

        //POST请求方式
        if (strtolower($method) == 'post') {
            if ($this->filterPostRequest($request, $rules)) {
                return true;
            }
            else {
                return false;
            }
        }
        // GET请求方法
        elseif (strtolower($method) == 'get') {
            if ($this->filterGetRequest($request, $rules)) {
                return true;
            }
            else {
                return false;
            }
        }
        // 其他
        else {
            echo $this->createErrorResponse('100','请求方式错误');
            return false;
        }
    }

    /* 过滤GET请求
     * @param request 请求对象
     * @param rules 过滤规则
     */
    protected function filterGetRequest($request, $rules) {
        //请求方法
        $method = $request->method();
        //请求方式错误
        if (strtolower($method) != 'get') {
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

	// 创建随机字符串
	protected function createRandomString($length) {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string ='';
        for ( $i = 0; $i < $length; $i++ )
        {

            $string .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $string;
    }
}
?>