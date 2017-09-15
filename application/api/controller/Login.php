<?php
//登录模块
namespace app\api\controller;

use think\Controller;
use think\Session;

use app\api\model\Login_log;
use app\api\controller\Base;
use app\api\model\User;
use app\api\modelLogin_log;

class Login extends Base 
{
	//登录方法
	public function index() {
        // 请求信息
        $request = request();

        // 验证规则
        $rules = [
            'account'		=>		'require',
            'password'		=>		'require'
        ];

        // POST过滤
        if (!$this->filterPostRequest($request, $rules)) {
            return;
        }

        // 账号
        $account = trim($request->param('account'));
        // 密码
        $password = trim($request->param('password'));

        //判断是否存在该用户
        $user = User::get([
            "account"   =>  $account,
            "password"  =>  $password
        ]);

        if (is_null($user)) {
            echo $this->createErrorResponse(300, '账号或密码错误');
            return;
        }

        // 用户ID
        $accountId  = $user->id;


        // 记录登录日志
        $loginLog = new Login_log([
            'user_id'       =>      $accountId,
            'logintime'     =>      time()
        ]);
        $loginLog->save();

        if (!$loginLog->id) {
            echo $this->createErrorResponse(301, "写入登录日志失败");
            return;
        }

        $accessToken = $this->createRandomString(16);

        Session::set('accessToken',$accessToken);

        // 返回的数据
        $data = [
            'accessToken'       =>      $accessToken,
            'accountId'         =>      $accountId
        ];

        echo $this->createSuccessResponse($data);
        return;
	}
}
?>