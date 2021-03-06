<?php
/**
 * Created by PhpStorm.
 * User: SoulJa
 * Date: 2017/9/17
 * Time: 下午1:20
 */
namespace app\api\controller;

use think\Controller;
use think\Db;
use app\api\controller\Base;
use app\api\model\Application;
use app\api\model\User;
use app\api\model\Application_user_view;


class App extends Base
{
    public function index() {
        // 请求信息
        $request = request();

        // 验证规则
        $rules = [
            'accessToken'   =>      'require'
        ];
        // POST过滤
        if (!$this->filterOnlineStatusRequest($request, $rules)) {
            return;
        }
        $list =collection(Application_user_view::all());
//        $list = Db::table('sdp_mobile_application')->alias('application')->join('sdp_mobile_user user','application.user_id = user.id')->field(['application.id','appname','access_key_id','access_key_secret','appkey','user_id','application.createtime','user.account'])->select();
        echo $this->createSuccessResponse(['appList' => $list]);
        return;
    }

    // 添加应用
    public function addApp() {
        // 请求信息
        $request = request();

        // 验证规则
        $rules = [
            'appname'		=>		'require',
            'access_key_id'		=>		'require',
            'access_key_secret'   =>      'require',
            'appkey'        =>      'require',
            'user_id'        =>      'require',
            'accessToken'   =>      'require'
        ];
        // POST过滤
        if (!$this->filterOnlineStatusRequest($request, $rules)) {
            return;
        }

        // 应用名称
        $appname = trim($request->param('appname'));
        // 应用校验ID
        $access_key_id = trim($request->param('access_key_id'));
        // 应用校验secret
        $access_key_secret = trim($request->param('access_key_secret'));
        // 应用的Key
        $appkey = trim($request->param('appkey'));
        // 用户ID
        $user_id = trim($request->param('user_id'));
        // 会话
        $accessToken = trim($request->param('accessToken'));

        // 校验应用名称
        $app = Application::get(['appname'=>$appname]);

        if (!is_null($app)) {
            echo $this->createErrorResponse(4000, '已经存在该应用');
            return;
        }

        // 是否存在该用户
        $user = User::get(['id' => $user_id]);

        if (is_null($user)) {
            echo $this->createErrorResponse(4001,'不存在的用户');
            return;
        }
        //写入数据库
        $data = [
            'appname'       =>      $appname,
            'access_key_id'      =>      $access_key_id,
            'access_key_secret'    =>      $access_key_secret,
            'appkey'        =>      $appkey,
            'user_id'        =>     $user_id,
            'createtime'    =>      time()
        ];
        $app = new Application($data);
        $app->save();
        // 写入数据库失败
        if (!$app->id) {
            echo $this->createErrorResponse(4002, '添加应用失败');
            return;
        } else {
            // 注册成功
            echo $this->createSuccessResponse($data);
            return;
        }
    }
}
?>