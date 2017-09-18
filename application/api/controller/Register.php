<?php
//注册模块
namespace app\api\controller;

use think\Controller;
use app\api\controller\Base;
use app\api\model\User;

class Register extends Base 
{
	// 注册方法
    public function index() {
		// 请求信息
		$request = request();
		
		// 验证规则
		$rules = [
			'account'		=>		'require',
			'password'		=>		'require',
			'rePassword'	=>		'require'
		];

		// POST过滤
		if (!$this->filterPostRequest($request, $rules)) {
			return;
		}
		// 账号
		$account = trim($request->param('account'));
		// 密码
		$password = trim($request->param('password'));
		// 重复密码
		$rePassword = trim($request->param('rePassword'));

		//判断密码是否一致
		if ($password != $rePassword) {
			echo $this->createErrorResponse(2000, '请输入一致的密码');
			return;
		}
		// 判断是否存在该用户
        $user = User::get(['account'=>$account]);
		// 存在该用户
        if ($user != null) {
            echo $this->createErrorResponse(2001,'账号已存在');
            return;
        }
        //写入数据库
        $user = new User([
            'account'       =>      $account,
            'password'      =>      $password,
            'createtime'    =>      time()
        ]);
        $user->save();
        // 写入数据库失败
        if (!$user->id) {
            echo $this->createErrorResponse(2002, '账号注册失败');
            return;
        } else {
            // 注册成功
            echo $this->createSuccessResponse('');
        }
	}
}
?>