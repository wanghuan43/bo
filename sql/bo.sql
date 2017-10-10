/*
 Navicat Premium Data Transfer

 Source Server         : BO
 Source Server Type    : MySQL
 Source Server Version : 50719
 Source Host           : 47.91.20.83
 Source Database       : bo

 Target Server Type    : MySQL
 Target Server Version : 50719
 File Encoding         : utf-8

 Date: 09/30/2017 11:23:36 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `kj_acceptance`
-- ----------------------------
DROP TABLE IF EXISTS `kj_acceptance`;
CREATE TABLE `kj_acceptance` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `a_no` char(50) NOT NULL COMMENT '验收单号',
  `a_mid` int(11) NOT NULL COMMENT '责任人ID',
  `a_mname` varchar(200) NOT NULL COMMENT '责任人名',
  `a_content` text NOT NULL COMMENT '描述',
  `a_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:销售;2:采销',
  `a_coid` int(11) NOT NULL COMMENT '对方公司ID',
  `a_coname` varchar(200) NOT NULL COMMENT '对方公司名',
  `a_money` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `a_used` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已使用金额',
  `a_noused` decimal(18,2) unsigned NOT NULL DEFAULT '0.00',
  `a_date` int(10) NOT NULL COMMENT '验收时间',
  PRIMARY KEY (`a_id`,`a_no`,`a_mid`,`a_coid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='验收单表';

-- ----------------------------
--  Table structure for `kj_admin`
-- ----------------------------
DROP TABLE IF EXISTS `kj_admin`;
CREATE TABLE `kj_admin` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_mid` int(11) NOT NULL,
  PRIMARY KEY (`a_id`,`a_mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
--  Table structure for `kj_chances`
-- ----------------------------
DROP TABLE IF EXISTS `kj_chances`;
CREATE TABLE `kj_chances` (
  `cs_id` int(11) NOT NULL AUTO_INCREMENT,
  `cs_mid` int(11) DEFAULT NULL,
  `cs_mname` varchar(200) DEFAULT NULL,
  `cs_name` varchar(200) NOT NULL,
  `cs_isShow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:显示;2:不显示',
  `cs_createtime` int(10) NOT NULL,
  PRIMARY KEY (`cs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单成交机会表';

-- ----------------------------
--  Table structure for `kj_circulation`
-- ----------------------------
DROP TABLE IF EXISTS `kj_circulation`;
CREATE TABLE `kj_circulation` (
  `ci_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '传阅ID',
  `ci_mid` int(11) NOT NULL COMMENT '被传阅人ID',
  `ci_otid` int(11) NOT NULL COMMENT '被传阅ID',
  `ci_type` char(50) NOT NULL COMMENT '传阅model:列如：invoice=发票',
  PRIMARY KEY (`ci_id`,`ci_mid`,`ci_otid`),
  KEY `t` (`ci_mid`,`ci_otid`,`ci_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='传阅人关系表';

-- ----------------------------
--  Table structure for `kj_column`
-- ----------------------------
DROP TABLE IF EXISTS `kj_column`;
CREATE TABLE `kj_column` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_fid` int(11) NOT NULL COMMENT '计划单ID',
  `c_row` int(11) NOT NULL DEFAULT '1' COMMENT '行ID',
  `c_col` int(11) NOT NULL DEFAULT '1' COMMENT '列ID',
  `c_value` text COMMENT '列值',
  `c_permission` text COMMENT '权限',
  PRIMARY KEY (`c_id`,`c_fid`),
  KEY `column_row_col_fid` (`c_fid`,`c_row`,`c_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计划单行列值';

-- ----------------------------
--  Table structure for `kj_company`
-- ----------------------------
DROP TABLE IF EXISTS `kj_company`;
CREATE TABLE `kj_company` (
  `co_id` int(11) NOT NULL AUTO_INCREMENT,
  `co_code` varchar(20) NOT NULL COMMENT '编码',
  `co_name` varchar(200) NOT NULL COMMENT '公司名称',
  `co_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 => 供应商，2 => 客户',
  `co_internal_flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => 不是内部的供应商/客户，1=>内部的',
  `co_mnemonic_code` varchar(10) DEFAULT NULL COMMENT '助记码',
  `co_industry` varchar(50) DEFAULT NULL COMMENT '行业',
  `co_address` varchar(255) DEFAULT NULL COMMENT '地址',
  `co_tax_id` varchar(30) DEFAULT NULL COMMENT '税务登记号',
  `co_reg_id` varchar(30) DEFAULT NULL COMMENT '工商注册号',
  `co_lr` varchar(30) DEFAULT NULL COMMENT '法人代表',
  `co_status` tinyint(1) DEFAULT '1' COMMENT '0=>禁用，1=>核准',
  `co_internal_name` varchar(255) DEFAULT NULL COMMENT '集团内公司名称',
  `co_flag` tinyint(1) DEFAULT NULL COMMENT '委外商:0=>否,1=>是',
  `co_create_org` varchar(255) DEFAULT NULL COMMENT '创建管理单元/创建组织',
  `co_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `co_remark` varchar(255) DEFAULT NULL COMMENT '备注/简称',
  PRIMARY KEY (`co_id`),
  UNIQUE KEY `co_name_type` (`co_name`,`co_type`,`co_status`,`co_code`),
  KEY `t` (`co_name`),
  KEY `code` (`co_code`),
  KEY `m_code` (`co_mnemonic_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='主数据-公司表';

-- ----------------------------
--  Table structure for `kj_contract`
-- ----------------------------
DROP TABLE IF EXISTS `kj_contract`;
CREATE TABLE `kj_contract` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '合同主键',
  `c_pid` int(11) NOT NULL,
  `c_pname` varchar(255) NOT NULL,
  `c_no` char(50) NOT NULL COMMENT '合同号',
  `c_name` varchar(200) NOT NULL COMMENT '合同名称',
  `c_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:销售;2:采销',
  `c_money` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `c_used` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已使用金额',
  `c_noused` decimal(18,2) unsigned NOT NULL DEFAULT '0.00',
  `c_coid` int(11) NOT NULL COMMENT '公司主键',
  `c_coname` varchar(200) NOT NULL COMMENT '公司名称',
  `c_mid` int(11) DEFAULT NULL COMMENT '责任人主键',
  `c_mname` varchar(200) DEFAULT NULL COMMENT '责任人名称',
  `c_bakup` text COMMENT '备注',
  `c_date` int(10) NOT NULL,
  `c_createtime` int(10) NOT NULL COMMENT '创建时间',
  `c_updatetime` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`c_id`,`c_no`,`c_coid`,`c_pid`),
  KEY `contract_name_type_` (`c_name`,`c_type`,`c_mid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='合同表';

-- ----------------------------
--  Table structure for `kj_department`
-- ----------------------------
DROP TABLE IF EXISTS `kj_department`;
CREATE TABLE `kj_department` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `d_name` varchar(200) NOT NULL,
  `d_code` char(7) NOT NULL COMMENT '成本中心编码',
  `m_name` varchar(200) DEFAULT NULL COMMENT '部门领导姓名',
  `m_code` char(10) DEFAULT NULL COMMENT '部门领导编号',
  PRIMARY KEY (`d_id`),
  UNIQUE KEY `t` (`d_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='主数据-部门';

-- ----------------------------
--  Table structure for `kj_favorite`
-- ----------------------------
DROP TABLE IF EXISTS `kj_favorite`;
CREATE TABLE `kj_favorite` (
  `f_oid` int(11) NOT NULL,
  `f_mid` int(11) NOT NULL,
  PRIMARY KEY (`f_oid`,`f_mid`),
  KEY `t` (`f_oid`,`f_mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏表';

-- ----------------------------
--  Table structure for `kj_form`
-- ----------------------------
DROP TABLE IF EXISTS `kj_form`;
CREATE TABLE `kj_form` (
  `f_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(150) NOT NULL COMMENT '计划单名',
  `f_permission` text COMMENT '计划单权限',
  `f_gids` text COMMENT '计划单组合',
  PRIMARY KEY (`f_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计划单';

-- ----------------------------
--  Table structure for `kj_group`
-- ----------------------------
DROP TABLE IF EXISTS `kj_group`;
CREATE TABLE `kj_group` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_name` varchar(150) NOT NULL COMMENT '计划单组合',
  `g_fids` text COMMENT '计划单ID',
  `g_permission` text COMMENT '计划单组合权限',
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计划单组合';

-- ----------------------------
--  Table structure for `kj_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `kj_invoice`;
CREATE TABLE `kj_invoice` (
  `i_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `i_no` varchar(200) NOT NULL COMMENT '发票号',
  `i_mid` int(11) NOT NULL COMMENT '责任人主键',
  `i_mname` varchar(200) NOT NULL COMMENT '责任人名称',
  `i_content` varchar(255) DEFAULT NULL COMMENT '发票描述',
  `i_coid` int(11) NOT NULL COMMENT '公司主键',
  `i_coname` varchar(200) NOT NULL COMMENT '公司名称',
  `i_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:销售;2:采销',
  `i_tax` tinyint(1) NOT NULL DEFAULT '1',
  `i_money` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总价格',
  `i_used` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已使用价格',
  `i_noused` decimal(18,2) unsigned NOT NULL DEFAULT '0.00',
  `i_date` int(10) NOT NULL,
  PRIMARY KEY (`i_id`,`i_no`,`i_mid`,`i_coid`),
  KEY `kj_invoice_indexes` (`i_money`,`i_used`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='发票表';

-- ----------------------------
--  Table structure for `kj_logs`
-- ----------------------------
DROP TABLE IF EXISTS `kj_logs`;
CREATE TABLE `kj_logs` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `l_otid` int(11) DEFAULT NULL,
  `l_mid` int(11) NOT NULL COMMENT '操作人',
  `l_mname` varchar(200) NOT NULL COMMENT '操作人名',
  `l_opt` char(10) NOT NULL DEFAULT 'add',
  `l_new` longtext NOT NULL COMMENT '操作内容',
  `l_old` longtext NOT NULL,
  `l_model` char(50) NOT NULL COMMENT '操作model',
  `l_isadmin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:非管理员;1:管理员',
  `l_rollback` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:正常;1:回滚成功;2:回滚失败',
  `l_panding` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未审核;1:审核失败;2:审核成功',
  `l_createtime` int(10) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`l_id`,`l_mid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_member`
-- ----------------------------
DROP TABLE IF EXISTS `kj_member`;
CREATE TABLE `kj_member` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_code` char(10) NOT NULL COMMENT '编码',
  `m_is_lead` tinyint(1) NOT NULL DEFAULT '0',
  `m_name` varchar(200) NOT NULL,
  `m_email` varchar(200) NOT NULL,
  `m_phone` char(11) NOT NULL,
  `m_department` varchar(60) NOT NULL COMMENT '部门',
  `m_office` varchar(60) DEFAULT NULL COMMENT '科室',
  `m_did` int(3) NOT NULL COMMENT '部门ID',
  `m_ldid` int(3) DEFAULT NULL COMMENT '领导的部门ID',
  `m_ldname` varchar(60) DEFAULT NULL COMMENT '领导的部门名',
  `m_password` char(36) DEFAULT NULL,
  `m_isAdmin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:非管理员;1:管理员',
  PRIMARY KEY (`m_id`),
  UNIQUE KEY `c` (`m_code`),
  UNIQUE KEY `t` (`m_name`,`m_email`),
  KEY `a` (`m_is_lead`),
  KEY `b` (`m_did`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='主数据-人员';

-- ----------------------------
--  Table structure for `kj_menu`
-- ----------------------------
DROP TABLE IF EXISTS `kj_menu`;
CREATE TABLE `kj_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `flag` varchar(100) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:不显示；1：显示',
  `list_order` int(3) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `n` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_order_project`
-- ----------------------------
DROP TABLE IF EXISTS `kj_order_project`;
CREATE TABLE `kj_order_project` (
  `op_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `op_oid` int(11) NOT NULL COMMENT '订单主键',
  `op_date` int(10) NOT NULL COMMENT '预计完成时间',
  `op_month` char(50) DEFAULT NULL COMMENT '提前/延期时间',
  `op_percent` char(50) NOT NULL,
  `op_used` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '预计金额',
  `op_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:发票;2:验收单;3:回款',
  PRIMARY KEY (`op_id`,`op_oid`),
  KEY `t` (`op_type`,`op_oid`,`op_date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单计划表';

-- ----------------------------
--  Table structure for `kj_order_used`
-- ----------------------------
DROP TABLE IF EXISTS `kj_order_used`;
CREATE TABLE `kj_order_used` (
  `ou_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ou_oid` int(11) NOT NULL COMMENT '订单主键',
  `ou_otid` int(11) NOT NULL COMMENT '关联主键',
  `ou_used` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '核销金额',
  `ou_date` int(10) NOT NULL,
  `ou_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:发票;2:验收单;3:回款',
  PRIMARY KEY (`ou_id`,`ou_oid`,`ou_otid`),
  KEY `t` (`ou_oid`,`ou_otid`,`ou_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单已完成表';

-- ----------------------------
--  Table structure for `kj_orders`
-- ----------------------------
DROP TABLE IF EXISTS `kj_orders`;
CREATE TABLE `kj_orders` (
  `o_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `o_no` char(50) NOT NULL COMMENT '订单标号',
  `o_cid` int(11) DEFAULT NULL COMMENT '合同主键',
  `o_cno` char(50) DEFAULT NULL COMMENT '合同号',
  `o_mid` int(11) NOT NULL COMMENT '责任人主键',
  `o_mname` varchar(200) DEFAULT NULL COMMENT '责任人名',
  `o_pid` int(11) NOT NULL COMMENT '项目主键',
  `o_pname` varchar(200) DEFAULT NULL COMMENT '项目名称',
  `o_subject` varchar(200) NOT NULL COMMENT '订单标题',
  `o_type` tinyint(1) NOT NULL COMMENT '订单类型',
  `o_coid` int(11) NOT NULL COMMENT '公司主键',
  `o_coname` varchar(200) DEFAULT NULL COMMENT '公司名称',
  `o_lie` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:内部;2:外部',
  `o_did` int(11) NOT NULL COMMENT '部门主键',
  `o_dname` varchar(200) DEFAULT NULL COMMENT '部门名称',
  `o_csid` int(11) NOT NULL COMMENT '机会主键',
  `o_csname` varchar(200) DEFAULT NULL,
  `o_date` int(10) NOT NULL COMMENT '订单日期',
  `o_money` decimal(18,2) NOT NULL COMMENT '总金额',
  `o_num` decimal(8,2) DEFAULT NULL COMMENT '订单数量',
  `o_tax` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:3%;2:6% 增;3:6% 普;4:17% 增;5:17% 普;',
  `o_deal` int(11) NOT NULL COMMENT '成交机会',
  `o_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:接洽;2:意向;3:立项;4:招标;5:定标;6:合同',
  `o_profits` char(10) DEFAULT NULL COMMENT '利润率',
  `o_createtime` int(10) NOT NULL COMMENT '创建时间',
  `o_updatetime` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`o_id`,`o_no`,`o_mid`,`o_pid`,`o_did`,`o_coid`,`o_csid`),
  KEY `orders_subject_money_status` (`o_subject`,`o_money`,`o_status`,`o_coname`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
--  Table structure for `kj_permissions`
-- ----------------------------
DROP TABLE IF EXISTS `kj_permissions`;
CREATE TABLE `kj_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `opt` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:只读;2:读写',
  PRIMARY KEY (`id`,`menu_id`,`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_postil`
-- ----------------------------
DROP TABLE IF EXISTS `kj_postil`;
CREATE TABLE `kj_postil` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `p_oid` int(11) NOT NULL COMMENT '订单主键',
  `p_mid` int(11) NOT NULL COMMENT '批注人',
  `p_mname` varchar(200) NOT NULL COMMENT '批注人名',
  `p_title` varchar(255) NOT NULL,
  `p_content` text NOT NULL COMMENT '批注内容',
  `p_filename` varchar(255) DEFAULT NULL,
  `p_attachment` varchar(255) DEFAULT NULL COMMENT '批注附件',
  `p_createtime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`p_id`,`p_oid`,`p_mid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='批注表';

-- ----------------------------
--  Table structure for `kj_project`
-- ----------------------------
DROP TABLE IF EXISTS `kj_project`;
CREATE TABLE `kj_project` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_no` varchar(200) NOT NULL,
  `p_name` varchar(200) NOT NULL,
  PRIMARY KEY (`p_id`),
  KEY `t` (`p_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='主数据-项目';

-- ----------------------------
--  Table structure for `kj_received`
-- ----------------------------
DROP TABLE IF EXISTS `kj_received`;
CREATE TABLE `kj_received` (
  `r_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '回款主键',
  `r_no` varchar(200) NOT NULL COMMENT '回款编号',
  `r_mid` int(11) NOT NULL COMMENT '责任人主键',
  `r_mname` varchar(200) NOT NULL COMMENT '责任人名',
  `r_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:销售;2:采销;',
  `r_coid` int(11) NOT NULL COMMENT '公司主键',
  `r_coname` varchar(200) NOT NULL COMMENT '公司名称',
  `r_date` int(10) NOT NULL COMMENT '发生时间',
  `r_money` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `r_used` decimal(18,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已使用金额',
  `r_noused` decimal(18,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`r_id`,`r_no`,`r_mid`,`r_coid`),
  KEY `kj_received_indexes` (`r_money`,`r_date`,`r_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='回款表';

-- ----------------------------
--  Table structure for `kj_taglib`
-- ----------------------------
DROP TABLE IF EXISTS `kj_taglib`;
CREATE TABLE `kj_taglib` (
  `tl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tl_name` varchar(200) NOT NULL COMMENT 'tag名字',
  `tl_times` int(11) NOT NULL DEFAULT '0' COMMENT '使用次数',
  PRIMARY KEY (`tl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='标签表';

-- ----------------------------
--  Table structure for `kj_taglink`
-- ----------------------------
DROP TABLE IF EXISTS `kj_taglink`;
CREATE TABLE `kj_taglink` (
  `ot_id` int(11) NOT NULL,
  `tl_id` int(11) NOT NULL,
  `model` char(50) NOT NULL,
  PRIMARY KEY (`ot_id`,`tl_id`),
  KEY `tag_link_indexes` (`ot_id`,`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键字关联表';

-- ----------------------------
--  Table structure for `kj_turnover`
-- ----------------------------
DROP TABLE IF EXISTS `kj_turnover`;
CREATE TABLE `kj_turnover` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `m_id` int(11) DEFAULT NULL,
  `m_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:显示;2:不显示',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
