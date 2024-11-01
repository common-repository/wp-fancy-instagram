=== WP Fancy Instagram ===
Contributors: Bigfa
Tags: wordpress, Instagram
Requires at least: 4.0
Tested up to: 4.5.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an instagram photo wall and cache the photos to your host.

== Description ==

Add an instagram photo wall for photos of you and you liked and cache the photos to your host.



== Installation ==

1. 上传 `wp-fancy-instagram`目录 到 `/wp-content/plugins/` 目录
2. 在后台插件菜单激活该插件
3. 在http://galfond.com/get-instagram-token授权获得token后填入插件后台设置
4. 方法1：新建一个页面：文本框输入 [wfi][/wfi] 即可
5. 方法2：新建一个模板，使用下面的函数wp_fancy_instagram(); 到需要的位置，新建一个页面，使用上面的模板

== Upgrade Notice ==

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
= 1.0.2 =
* 后台可设置是否展示赞过的照片
* 给图片加上了灯箱效果
= 1.0.1 =
* 静态文件按需载入
* 针对Puma 主题特殊优化
= 1.0.0 =
* 增加了点赞照片
* 修复了无法加载所有照片的bug
* JS代码优化
* css样式优化
= 0.0.3 =
* 更新了获取API的方式，解决了无法正常获取全部照片的bug，需要后台重新设置参数。
= 0.0.2 =
* 修复bug
= 0.0.1 =
* 最初0.0.1版本发布

