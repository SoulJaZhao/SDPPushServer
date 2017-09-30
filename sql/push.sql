#创建数据库
CREATE DATABASE IF NOT EXISTS sdp_mobile;
#使用数据库
USE sdp_mobile;
#创建用户表
CREATE TABLE IF NOT EXISTS sdp_mobile_user (
  id INT AUTO_INCREMENT PRIMARY KEY COMMENT '用户主键',
  account VARCHAR(30) NOT NULL DEFAULT '' COMMENT '账号' UNIQUE,
  password VARCHAR(30) NOT NULL DEFAULT '' COMMENT '密码',
  createtime INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)
ENGINE MYISAM DEFAULT CHARSET=UTF8;
#创建登录日志表
CREATE TABLE IF NOT EXISTS sdp_mobile_login_log (
  id INT AUTO_INCREMENT PRIMARY KEY COMMENT '登录日志',
  user_id INT NOT NULL DEFAULT 0 COMMENT '登录用户ID',
  logintime INT NOT NULL DEFAULT 0 COMMENT '登录时间'
)
ENGINE MYISAM DEFAULT CHARSET=UTF8;
#创建应用列表
CREATE TABLE IF NOT EXISTS  sdp_mobile_application (
  id INT AUTO_INCREMENT PRIMARY KEY COMMENT '应用主键',
  appname VARCHAR(50) NOT NULL DEFAULT '' COMMENT '应用名称' UNIQUE,
  access_key_id VARCHAR(50) NOT NULL DEFAULT '' COMMENT '验证KEYID',
  access_key_secret VARCHAR(50) NOT NULL DEFAULT '' COMMENT '验证secret',
  appkey VARCHAR(50) NOT NULL DEFAULT '' COMMENT 'appkey',
  user_id INT NOT NULL DEFAULT 0 COMMENT '创建的用户ID',
  createtime INT NOT NULL DEFAULT 0 COMMENT '创建时间'
)
ENGINE MYISAM DEFAULT CHARSET=UTF8;
#创建应用用户视图
CREATE VIEW sdp_mobile_application_user_view as select application.id,application.appname,application.access_key_id,application.access_key_secret,application.appkey,application.user_id,application.createtime,user.account from sdp_mobile_application as application left JOIN sdp_mobile_user as user on application.user_id=user.id ORDER BY application.id;
#创建推送记录表
CREATE TABLE  IF NOT EXISTS sdp_mobile_push_record (
  id INT AUTO_INCREMENT PRIMARY KEY COMMENT '推送记录主键',
  app_id INT NOT NULL DEFAULT 0 COMMENT '应用ID',
  user_id INT NOT NULL DEFAULT 0 COMMENT '用户ID',
  target VARCHAR(30) NOT NULL DEFAULT '' COMMENT '推送目标',
  target_value VARCHAR(50) not NULL DEFAULT '' COMMENT '推送数据值',
  devicetype VARCHAR(20) NOT NULL DEFAULT '' COMMENT '推送设备类型',
  pushtype VARCHAR(20) NOT NULL DEFAULT '' COMMENT '推送类型',
  title VARCHAR(30) NOT NULL DEFAULT '' COMMENT '推送标题',
  body VARCHAR(50) NOT NULL DEFAULT '' COMMENT '推送具体信息',
  badge INT NOT NULL DEFAULT 0 COMMENT '推送的角标',
  silent VARCHAR(20) NOT NULL DEFAULT '' COMMENT '是否开启静默通知',
  apns VARCHAR(20) NOT NULL DEFAULT '' COMMENT '推送环境',
  pushtime INT NOT NULL DEFAULT 0 COMMENT '推送时间'
)
ENGINE MYISAM DEFAULT CHARSET=UTF8;

#创建查询视图
CREATE VIEW sdp_mobile_push_view as SELECT push.id,push.target,push.target_value,push.devicetype,push.pushtype,push.title,push.body,push.badge,push.silent,push.apns,push.pushtime,application.appname FROM sdp_mobile_push_record as push LEFT JOIN sdp_mobile_application as application ON push.app_id=application.id ORDER BY push.id DESC;