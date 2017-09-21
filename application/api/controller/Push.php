<?php
/**
 * Created by PhpStorm.
 * User: sdpmobile
 * Date: 2017/9/21
 * Time: 下午2:48
 */
// 推送通知
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Loader;

use app\api\model\User;
use app\api\model\Application;

class Push extends Base {
    /**
     *	初始化方法
     */
    public function _initialize() {
        //加载阿里云SDK
        Loader::import('aliyun-openapi-php-sdk-master.aliyun-php-sdk-core.Config', EXTEND_PATH);
        Loader::import('aliyun-openapi-php-sdk-master.aliyun-php-sdk-push.Push.Request.V20160801.PushRequest', EXTEND_PATH);
    }

    public function index() {
        // 请求信息
        $request = request();

        // 验证规则
        $rules = [
            'userId'        =>      'require',
            'accessToken'   =>      'require',
            'appId'         =>      'require',
            'target'        =>      'require',
            'targetValue'   =>      'require',
            'deviceType'    =>      'require',
            'pushType'      =>      'require',
            'title'         =>      'require',
            'body'          =>      'require',
            'badge'         =>      'require',
            'silent'        =>      'require',
            'apns'          =>      'require'
        ];
        // POST过滤
        if (!$this->filterOnlineStatusRequest($request, $rules)) {
            return;
        }

        // 参数赋值
        $userId = $request->param('userId');
        $accessToken = $request->param('accessToken');
        $appId = $request->param('appId');
        $target = $request->param('target');
        $targetValue = $request->param('targetValue');
        $deviceType = $request->param('deviceType');
        $pushType = $request->param('pushType');
        $title = $request->param('title');
        $body = $request->param('body');
        $badge = $request->param('badge');
        $silent = $request->param('silent');
        $apns = $request->param('apns');

        // 用户信息
        $user = User::get([
            'id'=> $userId
        ]);

        if (is_null($user)) {
            echo $this->createErrorResponse(5000,'不存在的用户');
            return;
        }

        // 应用信息
        $app = Application::get([
            'id'=>$appId
        ]);

        if (is_null($app)) {
            echo $this->createErrorResponse(5001,'不存在的应用');
            return;
        }

        // target 是否正确
        if (!$this->isInEnum($target, ['DEVICE','ACCOUNT','TAG','ALL'])) {
            echo $this->createErrorResponse(5002, '请输入正确的推送目标');
            return;
        }

        // deviceType 是否正确
        if (!$this->isInEnum($deviceType, ['ANDROID','iOS','ALL'])) {
            echo $this->createErrorResponse(5003, '请输入正确的设备类型');
            return;
        }

        // pushType 是否正确
        if (!$this->isInEnum($pushType, ['MESSAGE','NOTICE'])) {
            echo $this->createErrorResponse(5004, '请输入正确的推送类型');
            return;
        }

        $accessKeyId = $app->access_key_id;
        $accessKeySecret = $app->access_key_secret;
        $appkey = $app->appkey;

        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessKeySecret);
        $client = new \DefaultAcsClient($iClientProfile);
        $pushRequest = new \Push\Request\V20160801\PushRequest();
        // 推送目标
        $pushRequest->setAppKey($appkey);
        $pushRequest->setTarget($target); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
        $pushRequest->setTargetValue($targetValue); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
        $pushRequest->setDeviceType($deviceType); //设备类型 ANDROID iOS ALL.
        $pushRequest->setPushType($pushType); //消息类型 MESSAGE NOTICE
        $pushRequest->setTitle($title); // 消息的标题
        $pushRequest->setBody($body); // 消息的内容

        // 推送配置: iOS
        $pushRequest->setiOSBadge($badge); // iOS应用图标右上角角标
        $pushRequest->setiOSSilentNotification("true");//是否开启静默通知
        $pushRequest->setiOSMusic("default"); // iOS通知声音
        $pushRequest->setiOSApnsEnv($apns);//iOS的通知是通过APNs中心来发送的，需要填写对应的环境信息。"DEV" : 表示开发环境 "PRODUCT" : 表示生产环境
        $pushRequest->setiOSRemind("false"); // 推送时设备不在线（既与移动推送的服务端的长连接通道不通），则这条推送会做为通知，通过苹果的APNs通道送达一次(发送通知时,Summary为通知的内容,Message不起作用)。注意：离线消息转通知仅适用于生产环境
        $pushRequest->setiOSRemindBody("iOSRemindBody");//iOS消息转通知时使用的iOS通知内容，仅当iOSApnsEnv=PRODUCT && iOSRemind为true时有效
        $pushRequest->setiOSExtParameters("{\"k1\":\"ios\",\"k2\":\"v2\"}"); //自定义的kv结构,开发者扩展用 针对iOS设备

        // 推送控制
        $pushTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+3 second'));//延迟3秒发送
        $pushRequest->setPushTime($pushTime);
        $expireTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 day'));//设置失效时间为1天
        $pushRequest->setExpireTime($expireTime);
        $pushRequest->setStoreOffline("false"); // 离线消息是否保存,若保存, 在推送时候，用户即使不在线，下一次上线则会收到

        $response = $client->getAcsResponse($pushRequest);

        if ($response->MessageId) {
            echo $this->createSuccessResponse($response);
            return;
        } else {
            echo $this->createErrorResponse(5005,'推送失败');
            return;
        }
    }
}