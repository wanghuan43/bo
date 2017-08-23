/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50505
 Source Host           : localhost
 Source Database       : bo

 Target Server Type    : MySQL
 Target Server Version : 50505
 File Encoding         : utf-8

 Date: 08/23/2017 13:23:45 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `kj_acceptance`
-- ----------------------------
DROP TABLE IF EXISTS `kj_acceptance`;
CREATE TABLE `kj_acceptance` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_no` char(50) NOT NULL,
  `a_mid` int(11) NOT NULL,
  `a_mname` varchar(200) NOT NULL,
  `a_content` varchar(255) NOT NULL,
  `a_type` int(11) NOT NULL,
  `a_coid` int(11) NOT NULL,
  `a_coname` varchar(200) NOT NULL,
  `a_money` decimal(18,2) NOT NULL DEFAULT '0.00',
  `a_used` decimal(18,2) NOT NULL DEFAULT '0.00',
  `a_date` int(10) NOT NULL,
  PRIMARY KEY (`a_id`,`a_no`,`a_mid`,`a_type`,`a_coid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_circulation`
-- ----------------------------
DROP TABLE IF EXISTS `kj_circulation`;
CREATE TABLE `kj_circulation` (
  `ci_id` int(11) NOT NULL AUTO_INCREMENT,
  `ci_mid` int(11) NOT NULL,
  `ci_otid` int(11) NOT NULL,
  `ci_type` char(50) NOT NULL,
  PRIMARY KEY (`ci_id`,`ci_mid`,`ci_otid`),
  KEY `t` (`ci_mid`,`ci_otid`,`ci_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_company`
-- ----------------------------
DROP TABLE IF EXISTS `kj_company`;
CREATE TABLE `kj_company` (
  `co_id` int(11) NOT NULL AUTO_INCREMENT,
  `co_name` varchar(200) NOT NULL,
  PRIMARY KEY (`co_id`),
  KEY `t` (`co_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_contract`
-- ----------------------------
DROP TABLE IF EXISTS `kj_contract`;
CREATE TABLE `kj_contract` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_no` char(50) NOT NULL,
  `c_name` varchar(200) NOT NULL,
  `c_type` int(11) NOT NULL,
  `c_money` decimal(18,2) NOT NULL DEFAULT '0.00',
  `c_used` decimal(18,2) NOT NULL DEFAULT '0.00',
  `c_coid` varchar(200) NOT NULL,
  `c_coname` varchar(200) NOT NULL,
  `c_mid` int(11) NOT NULL,
  `c_mname` varchar(200) DEFAULT NULL,
  `c_bakup` varchar(255) DEFAULT NULL,
  `c_createtime` int(10) NOT NULL,
  `c_updatetime` int(10) NOT NULL,
  PRIMARY KEY (`c_id`,`c_no`,`c_coid`,`c_mid`,`c_type`),
  KEY `contract_name_type_` (`c_name`,`c_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_department`
-- ----------------------------
DROP TABLE IF EXISTS `kj_department`;
CREATE TABLE `kj_department` (
  `d_id` int(11) NOT NULL,
  `d_name` varchar(200) NOT NULL,
  PRIMARY KEY (`d_id`),
  KEY `t` (`d_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_favorite`
-- ----------------------------
DROP TABLE IF EXISTS `kj_favorite`;
CREATE TABLE `kj_favorite` (
  `f_oid` int(11) NOT NULL,
  `f_mid` int(11) NOT NULL,
  PRIMARY KEY (`f_oid`,`f_mid`),
  KEY `t` (`f_oid`,`f_mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `kj_invoice`;
CREATE TABLE `kj_invoice` (
  `i_id` int(11) NOT NULL AUTO_INCREMENT,
  `i_no` varchar(200) NOT NULL,
  `i_mid` int(11) NOT NULL,
  `i_mname` varchar(200) NOT NULL,
  `i_content` varchar(255) DEFAULT NULL,
  `i_coid` int(11) NOT NULL,
  `i_coname` varchar(200) NOT NULL,
  `i_type` int(11) NOT NULL,
  `i_tax` int(11) NOT NULL,
  `i_money` decimal(18,2) NOT NULL DEFAULT '0.00',
  `i_used` decimal(18,2) NOT NULL DEFAULT '0.00',
  `i_createtime` int(10) NOT NULL,
  `i_updatetime` int(10) NOT NULL,
  PRIMARY KEY (`i_id`,`i_no`,`i_mid`,`i_coid`,`i_type`,`i_tax`),
  KEY `kj_invoice_indexes` (`i_money`,`i_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_logs`
-- ----------------------------
DROP TABLE IF EXISTS `kj_logs`;
CREATE TABLE `kj_logs` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_mid` int(11) NOT NULL,
  `l_mname` varchar(200) NOT NULL,
  `l_content` varchar(255) NOT NULL,
  `l_model` char(50) NOT NULL,
  `l_createtime` int(10) NOT NULL,
  PRIMARY KEY (`l_id`,`l_mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_member`
-- ----------------------------
DROP TABLE IF EXISTS `kj_member`;
CREATE TABLE `kj_member` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_name` varchar(200) NOT NULL,
  `m_email` varchar(200) NOT NULL,
  PRIMARY KEY (`m_id`),
  KEY `t` (`m_name`,`m_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_orderProject`
-- ----------------------------
DROP TABLE IF EXISTS `kj_orderProject`;
CREATE TABLE `kj_orderProject` (
  `op_id` int(11) NOT NULL AUTO_INCREMENT,
  `o_id` int(11) NOT NULL,
  `op_date` int(10) NOT NULL,
  `op_month` char(50) DEFAULT NULL,
  `op_used` decimal(18,2) NOT NULL DEFAULT '0.00',
  `op_type` varchar(255) NOT NULL DEFAULT '1' COMMENT '1:发票;2:验收单;3:回款',
  PRIMARY KEY (`op_id`,`o_id`),
  KEY `t` (`op_type`,`o_id`,`op_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_orderUsed`
-- ----------------------------
DROP TABLE IF EXISTS `kj_orderUsed`;
CREATE TABLE `kj_orderUsed` (
  `ou_id` int(11) NOT NULL AUTO_INCREMENT,
  `o_id` int(11) NOT NULL,
  `ot_id` int(11) NOT NULL,
  `op_used` decimal(18,2) NOT NULL DEFAULT '0.00',
  `op_type` varchar(255) NOT NULL DEFAULT '1' COMMENT '1:发票;2:验收单;3:回款',
  PRIMARY KEY (`ou_id`,`o_id`,`ot_id`),
  KEY `t` (`o_id`,`ot_id`,`op_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_orders`
-- ----------------------------
DROP TABLE IF EXISTS `kj_orders`;
CREATE TABLE `kj_orders` (
  `o_id` int(11) NOT NULL AUTO_INCREMENT,
  `o_no` char(50) NOT NULL,
  `o_cid` int(11) DEFAULT NULL,
  `o_cno` char(50) DEFAULT NULL,
  `o_mid` int(11) NOT NULL,
  `o_mname` varchar(200) DEFAULT NULL,
  `o_pid` int(11) NOT NULL,
  `o_pname` varchar(200) DEFAULT NULL,
  `o_subject` varchar(200) NOT NULL,
  `o_type` int(11) NOT NULL,
  `o_coid` int(11) NOT NULL,
  `o_coname` varchar(200) DEFAULT NULL,
  `o_lie` varchar(255) NOT NULL DEFAULT '1',
  `o_did` int(11) NOT NULL,
  `o_dname` varchar(200) DEFAULT NULL,
  `o_date` int(10) NOT NULL,
  `o_money` decimal(18,2) NOT NULL,
  `o_num` decimal(8,2) DEFAULT NULL,
  `o_tax` int(11) NOT NULL,
  `o_deal` int(11) NOT NULL,
  `o_status` varchar(255) NOT NULL DEFAULT '1',
  `o_profits` char(10) DEFAULT NULL,
  `o_createtime` int(10) NOT NULL,
  `o_updatetime` int(10) NOT NULL,
  PRIMARY KEY (`o_id`,`o_no`,`o_mid`,`o_pid`,`o_did`,`o_coid`,`o_type`,`o_tax`),
  KEY `orders_subject_money_status` (`o_subject`,`o_money`,`o_status`,`o_coname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_postil`
-- ----------------------------
DROP TABLE IF EXISTS `kj_postil`;
CREATE TABLE `kj_postil` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_oid` int(11) NOT NULL,
  `p_mid` int(11) NOT NULL,
  `p_mname` varchar(200) NOT NULL,
  `p_content` varchar(255) NOT NULL,
  `p_attachment` varchar(255) DEFAULT NULL,
  `p_createtime` int(10) NOT NULL,
  PRIMARY KEY (`p_id`,`p_oid`,`p_mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_project`
-- ----------------------------
DROP TABLE IF EXISTS `kj_project`;
CREATE TABLE `kj_project` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(200) NOT NULL,
  PRIMARY KEY (`p_id`),
  KEY `t` (`p_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_received`
-- ----------------------------
DROP TABLE IF EXISTS `kj_received`;
CREATE TABLE `kj_received` (
  `r_id` int(11) NOT NULL AUTO_INCREMENT,
  `r_no` varchar(200) NOT NULL,
  `r_mid` int(11) NOT NULL,
  `r_mname` varchar(200) NOT NULL,
  `r_type` int(11) NOT NULL DEFAULT '1',
  `r_coid` int(11) NOT NULL,
  `r_coname` varchar(200) NOT NULL,
  `r_date` int(10) NOT NULL,
  `r_money` decimal(18,2) NOT NULL DEFAULT '0.00',
  `r_uesd` decimal(18,2) NOT NULL DEFAULT '0.00',
  `r_createtime` int(10) NOT NULL,
  `r_updatetime` int(10) NOT NULL,
  PRIMARY KEY (`r_id`,`r_no`,`r_mid`,`r_coid`,`r_type`),
  KEY `kj_received_indexes` (`r_money`,`r_uesd`,`r_date`,`r_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_tab_link`
-- ----------------------------
DROP TABLE IF EXISTS `kj_tab_link`;
CREATE TABLE `kj_tab_link` (
  `ot_id` int(11) NOT NULL,
  `tl_id` int(11) NOT NULL,
  PRIMARY KEY (`ot_id`,`tl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `kj_tablib`
-- ----------------------------
DROP TABLE IF EXISTS `kj_tablib`;
CREATE TABLE `kj_tablib` (
  `tl_id` int(11) NOT NULL AUTO_INCREMENT,
  `tl_name` varchar(200) NOT NULL,
  `tl_times` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
