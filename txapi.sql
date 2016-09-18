-- phpMyAdmin SQL Dump
-- version 4.4.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-10-14 18:59:25
-- 服务器版本： 5.6.23
-- PHP Version: 5.6.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `txapi`
--

-- --------------------------------------------------------

--
-- 表的结构 `tx_collected_user`
--

CREATE TABLE IF NOT EXISTS `tx_collected_user` (
  `cid` int(10) unsigned NOT NULL COMMENT '自增ID',
  `gid` int(10) unsigned NOT NULL COMMENT 'gid',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `chip_id` int(10) unsigned NOT NULL COMMENT '碎片ID',
  `is_valid` int(10) unsigned NOT NULL COMMENT '是否有效',
  `created_at` int(11) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收集碎片用户表';

-- --------------------------------------------------------

--
-- 表的结构 `tx_pic_game`
--

CREATE TABLE IF NOT EXISTS `tx_pic_game` (
  `gid` int(10) unsigned NOT NULL COMMENT '游戏ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `pic_id` int(10) unsigned NOT NULL COMMENT '图片ID',
  `collected_list` varchar(100) NOT NULL COMMENT '已收集碎片ID',
  `is_all` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否收集完成',
  `created_at` int(11) NOT NULL COMMENT '开始时间',
  `expired_at` int(11) NOT NULL COMMENT '结束时间',
  `finished_at` int(11) DEFAULT NULL COMMENT '完成时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='拼图游戏';

-- --------------------------------------------------------

--
-- 表的结构 `tx_wxuser`
--

CREATE TABLE IF NOT EXISTS `tx_wxuser` (
  `uid` int(10) unsigned NOT NULL COMMENT '自增ID',
  `openid` varchar(255) NOT NULL COMMENT '微信用户openid',
  `nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像',
  `unionid` varchar(255) DEFAULT NULL COMMENT '微信用户unionid',
  `created_at` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='微信用户表';

--
-- 转存表中的数据 `tx_wxuser`
--

INSERT INTO `tx_wxuser` (`uid`, `openid`, `nickname`, `headimgurl`, `unionid`, `created_at`) VALUES
(5, 'oXlLiwOc_CpE61yQBnYAGbbiLfHc', 'abin', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM7XY7Me0CkhTs7IZhXsXgzy3sOBxBgKYyAmUeNOuAb5mZBPlJBt2D0JqvSB7avqc7DeSEXrlQ9Tdibr8qB64VlUZkqicsc5wHPps/0', 'o_TtywyV9_cW1KJ90zK48eWPAWs4', 1444810258);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tx_collected_user`
--
ALTER TABLE `tx_collected_user`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `tx_pic_game`
--
ALTER TABLE `tx_pic_game`
  ADD PRIMARY KEY (`gid`);

--
-- Indexes for table `tx_wxuser`
--
ALTER TABLE `tx_wxuser`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tx_collected_user`
--
ALTER TABLE `tx_collected_user`
  MODIFY `cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID';
--
-- AUTO_INCREMENT for table `tx_pic_game`
--
ALTER TABLE `tx_pic_game`
  MODIFY `gid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '游戏ID';
--
-- AUTO_INCREMENT for table `tx_wxuser`
--
ALTER TABLE `tx_wxuser`
  MODIFY `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
