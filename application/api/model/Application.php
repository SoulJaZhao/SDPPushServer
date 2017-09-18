<?php
/**
 * Created by PhpStorm.
 * User: SoulJa
 * Date: 2017/9/17
 * Time: 下午1:35
 */
// 用户模型
namespace app\api\model;
use think\Model;
use think\model\Merge;

class Application extends Merge {
    // 定义关联模型列表
    protected $relationModel = ['User'];
    // 定义关联外键
    protected $fk = 'id';
    protected $mapFields = [
        // 为混淆字段定义映射
        'id'        =>  'Application.id',
        'user_id' =>  'User.id',
        'appCreatetime'    =>  'Application.createtime',
        'userCreatetime'    => 'User.createtime',
    ];
}