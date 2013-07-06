/*
MySQL Data Transfer
Source Host: 127.0.0.1
Source Database: teamdota_open
Target Host: 127.0.0.1
Target Database: teamdota_open
Date: 2013-7-7 0:20:27
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for admin_log
-- ----------------------------
CREATE TABLE `admin_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(60) NOT NULL DEFAULT '',
  `method` char(10) NOT NULL,
  `query` varchar(200) NOT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for admin_op_logs
-- ----------------------------
CREATE TABLE `admin_op_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(50) NOT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `op` varchar(20) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `op` (`op`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for admin_type
-- ----------------------------
CREATE TABLE `admin_type` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `typename` varchar(30) NOT NULL DEFAULT '',
  `purviews` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
CREATE TABLE `admin_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `usertype` smallint(5) NOT NULL,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `realname` varchar(30) DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_company
-- ----------------------------
CREATE TABLE `e_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `ctype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_discussion
-- ----------------------------
CREATE TABLE `e_discussion` (
  `discussion_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT '',
  `othertype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `otherid` int(11) unsigned NOT NULL DEFAULT '0',
  `lastpost` int(10) unsigned NOT NULL DEFAULT '0',
  `lastposter` varchar(50) NOT NULL DEFAULT '',
  `navidescription` varchar(255) NOT NULL DEFAULT '',
  `useip` varchar(15) NOT NULL DEFAULT '0',
  `post_num` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`discussion_id`),
  KEY `uid` (`uid`),
  KEY `lastpost` (`lastpost`),
  KEY `project_id` (`project_id`,`lastpost`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_document
-- ----------------------------
CREATE TABLE `e_document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT '',
  `uptime` int(10) unsigned NOT NULL DEFAULT '0',
  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`document_id`),
  KEY `project_id` (`project_id`,`uptime`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_file
-- ----------------------------
CREATE TABLE `e_file` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `fileurl` varchar(255) NOT NULL DEFAULT '',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT '',
  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `remote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `width` int(10) unsigned NOT NULL DEFAULT '32',
  `height` int(10) unsigned NOT NULL DEFAULT '32',
  `invisible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `downloads` int(11) unsigned NOT NULL DEFAULT '0',
  `filetype` varchar(50) NOT NULL DEFAULT '',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `project_id` (`project_id`,`logtime`),
  KEY `discussion_id` (`discussion_id`,`logtime`),
  KEY `post_id` (`post_id`,`logtime`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_file_short
-- ----------------------------
CREATE TABLE `e_file_short` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `fileurl` varchar(255) NOT NULL DEFAULT '',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT '',
  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `remote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `width` int(10) unsigned NOT NULL DEFAULT '32',
  `height` int(10) unsigned NOT NULL DEFAULT '32',
  `invisible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `downloads` int(11) unsigned NOT NULL DEFAULT '0',
  `filetype` varchar(50) NOT NULL DEFAULT '',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `project_id` (`project_id`,`logtime`),
  KEY `discussion_id` (`discussion_id`,`logtime`),
  KEY `post_id` (`post_id`,`logtime`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_group
-- ----------------------------
CREATE TABLE `e_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `gtype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  `attachsize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `maxattachsize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `all_project_num` int(10) unsigned NOT NULL DEFAULT '0',
  `project_num` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_group_member
-- ----------------------------
CREATE TABLE `e_group_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `ntype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_create_project` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_id_uid` (`group_id`,`uid`),
  KEY `group_id` (`group_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_invite
-- ----------------------------
CREATE TABLE `e_invite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT '',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `code` varchar(32) NOT NULL DEFAULT '',
  `fuid` int(11) unsigned NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  `sendtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_log_forgot_password
-- ----------------------------
CREATE TABLE `e_log_forgot_password` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL DEFAULT '',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_mailcron
-- ----------------------------
CREATE TABLE `e_mailcron` (
  `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `touid` int(11) unsigned NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '',
  `sendtime` int(10) unsigned NOT NULL DEFAULT '0',
  `hash_data` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`cid`),
  KEY `sendtime` (`sendtime`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_mailqueue
-- ----------------------------
CREATE TABLE `e_mailqueue` (
  `qid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL DEFAULT '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`qid`),
  KEY `mcid` (`cid`,`dateline`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_member
-- ----------------------------
CREATE TABLE `e_member` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `fullname` varchar(50) NOT NULL DEFAULT '',
  `ntype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `regip` varchar(15) NOT NULL DEFAULT '',
  `regdate` int(10) unsigned NOT NULL DEFAULT '0',
  `lastloginip` int(10) unsigned NOT NULL DEFAULT '0',
  `lastlogintime` int(10) unsigned NOT NULL DEFAULT '0',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `bday` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `salt` varchar(6) NOT NULL DEFAULT '',
  `is_create_project` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeoffset` tinyint(3) NOT NULL DEFAULT '8',
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `isavatar` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issubscribe` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `group_id` (`group_id`,`regdate`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_notice_attachment
-- ----------------------------
CREATE TABLE `e_notice_attachment` (
  `file_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uids` text NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_notice_discussion
-- ----------------------------
CREATE TABLE `e_notice_discussion` (
  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uids` text NOT NULL,
  PRIMARY KEY (`discussion_id`),
  KEY `discussion_id` (`discussion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_notification
-- ----------------------------
CREATE TABLE `e_notification` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_name` varchar(50) NOT NULL DEFAULT '',
  `sender_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sender_author` varchar(50) NOT NULL DEFAULT '',
  `title_html` text NOT NULL,
  `title_text` text NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text NOT NULL,
  `href` varchar(255) NOT NULL DEFAULT '',
  `object_id` int(11) unsigned NOT NULL DEFAULT '0',
  `object_type` varchar(15) NOT NULL DEFAULT '',
  `icon_url` varchar(30) NOT NULL DEFAULT '',
  `icon_op` varchar(30) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`),
  KEY `sender_id` (`sender_id`,`created_time`),
  KEY `created_time` (`created_time`),
  KEY `icon_url` (`object_id`,`icon_url`),
  KEY `object_id` (`object_id`,`object_type`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_plans_order
-- ----------------------------
CREATE TABLE `e_plans_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `plan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alipay_orderid` varchar(100) NOT NULL DEFAULT '',
  `alipay_username` varchar(255) NOT NULL DEFAULT '',
  `expires_year` int(10) unsigned NOT NULL DEFAULT '0',
  `expires_month` int(10) unsigned NOT NULL DEFAULT '0',
  `remarks` varchar(255) NOT NULL DEFAULT '',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alipay_orderid` (`alipay_orderid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_post
-- ----------------------------
CREATE TABLE `e_post` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `discussion_id` int(11) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `author` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`post_id`),
  KEY `discussion_id` (`discussion_id`,`logtime`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_project
-- ----------------------------
CREATE TABLE `e_project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `orderid` int(11) unsigned NOT NULL DEFAULT '0',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `useip` varchar(15) NOT NULL DEFAULT '',
  `discussion_num` int(11) unsigned NOT NULL DEFAULT '0',
  `file_num` int(11) unsigned NOT NULL DEFAULT '0',
  `document_num` int(11) unsigned NOT NULL DEFAULT '0',
  `attachsize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `member_num` int(11) unsigned NOT NULL DEFAULT '0',
  `todoslist_num` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_project_member
-- ----------------------------
CREATE TABLE `e_project_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_uid` (`project_id`,`uid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_searchindex
-- ----------------------------
CREATE TABLE `e_searchindex` (
  `searchid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `srchmod` tinyint(3) unsigned NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `searchstring` text NOT NULL,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `expiration` int(10) unsigned NOT NULL DEFAULT '0',
  `num` smallint(6) unsigned NOT NULL DEFAULT '0',
  `ids` text NOT NULL,
  PRIMARY KEY (`searchid`),
  KEY `srchmod` (`srchmod`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_session
-- ----------------------------
CREATE TABLE `e_session` (
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `lastactivity` (`lastactivity`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_timesheet
-- ----------------------------
CREATE TABLE `e_timesheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `de_begin_time` varchar(255) NOT NULL DEFAULT '00:00',
  `gu_end_time` varchar(255) NOT NULL DEFAULT '00:00',
  `fact_end_time` varchar(255) NOT NULL DEFAULT '00:00',
  `gu_desgin_crcle` int(11) NOT NULL,
  `fact_design_crcle` int(11) NOT NULL,
  `logtime` int(10) NOT NULL,
  `useip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `company_id` (`group_id`),
  KEY `task_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_todos
-- ----------------------------
CREATE TABLE `e_todos` (
  `todos_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `author` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `orderid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`todos_id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_order` (`project_id`,`orderid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_todoslist
-- ----------------------------
CREATE TABLE `e_todoslist` (
  `todoslist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `todos_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `author` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `assign_uid` int(11) unsigned NOT NULL DEFAULT '0',
  `assign_author` varchar(100) NOT NULL DEFAULT '',
  `due_date` int(10) unsigned NOT NULL DEFAULT '0',
  `completed_uid` int(11) unsigned NOT NULL DEFAULT '0',
  `completed_author` varchar(100) NOT NULL DEFAULT '',
  `completed_date` int(10) unsigned NOT NULL DEFAULT '0',
  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
  `post_num` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `logtime` int(10) unsigned NOT NULL DEFAULT '0',
  `orderid` int(11) unsigned NOT NULL DEFAULT '0',
  `is_completed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`todoslist_id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_order` (`project_id`,`orderid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_trash_can
-- ----------------------------
CREATE TABLE `e_trash_can` (
  `trash_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_name` varchar(50) NOT NULL,
  `sender_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sender_author` varchar(50) NOT NULL DEFAULT '',
  `title_html` text NOT NULL,
  `title_text` text NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text NOT NULL,
  `href` varchar(255) NOT NULL DEFAULT '',
  `object_id` int(11) unsigned NOT NULL DEFAULT '0',
  `object_type` varchar(15) NOT NULL DEFAULT '',
  `icon_op` varchar(30) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`trash_id`),
  KEY `sender_id` (`sender_id`,`created_time`),
  KEY `created_time` (`created_time`),
  KEY `object_id` (`object_id`,`object_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for e_trash_can_log
-- ----------------------------
CREATE TABLE `e_trash_can_log` (
  `trash_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) unsigned NOT NULL DEFAULT '0',
  `object_type` varchar(15) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`trash_id`),
  KEY `object_id` (`object_id`,`object_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `admin_type` VALUES ('1', '超级管理员', 'admin_AllowAll ');
INSERT INTO `admin_user` VALUES ('1', '1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1268461570', '1339168574', '127.0.0.1', '');