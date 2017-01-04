title: 'nginx启动，重启，关闭命令'
date: 2016-11-14 09:53:29
categories: 技术
tags: nginx
---
停止操作
停止操作是通过向nginx进程发送信号（什么是信号请参阅linux文 章）来进行的
步骤1：查询nginx主进程号
ps -ef | grep nginx
在进程列表里 面找master进程，它的编号就是主进程号了。
步骤2：发送信号
从容停止Nginx：
kill -QUIT 主进程号
快速停止Nginx：
kill -TERM 主进程号
强制停止Nginx：
pkill -9 nginx
<!-- more -->

另外， 若在nginx.conf配置了pid文件存放路径则该文件存放的就是Nginx主进程号，如果没指定则放在nginx的logs目录下。有了pid文 件，我们就不用先查询Nginx的主进程号，而直接向Nginx发送信号了，命令如下：
kill -信号类型 '/usr/nginx/logs/nginx.pid'

平滑重启
如果更改了配置就要重启Nginx，要先关闭Nginx再打开？不是的，可以向Nginx 发送信号，平滑重启。
平滑重启命令：
kill -HUP 住进称号或进程号文件路径
或者使用

/usr/nginx/sbin/nginx -s reload

注意，修改了配置文件后最好先检查一下修改过的配置文件是否正 确，以免重启后Nginx出现错误影响服务器稳定运行。判断Nginx配置是否正确命令如下：
nginx -t -c /usr/nginx/conf/nginx.conf
或者

/usr/nginx/sbin/nginx -t

平滑升级
如果服务器正在运行的Nginx要进行升级、添加或删除模块时，我们需 要停掉服务器并做相应修改，这样服务器就要在一段时间内停止服务，Nginx可以在不停机的情况下进行各种升级动作而不影响服务器运行。
步骤1：
如 果升级Nginx程序，先用新程序替换旧程序文件，编译安装的话新程序直接编译到Nginx安装目录中。
步 骤2：执行命令
kill -USR2 旧版程序的主进程号或进程文件名
此时旧的Nginx主进程将会把自己的进程文件改名为.oldbin，然后执行新版 Nginx。新旧Nginx会同市运行，共同处理请求。
这时要逐步停止旧版 Nginx，输入命令：
kill -WINCH 旧版主进程号
慢慢旧的工作进程就都会随着任务执行完毕而退出，新版的Nginx的工作进程会逐渐取代旧版 工作进程。

此 时，我们可以决定使用新版还是恢复到旧版。
不重载配置启动新/旧工作进程
kill -HUP 旧/新版主进程号
从容关闭旧/新进程
kill -QUIT 旧/新主进程号
如果此时报错，提示还有进程没有结束就用下面命令先关闭旧/新工作进程，再关闭主进程号：
kill -TERM 旧/新工作进程号

这样下来，如果要恢复到旧版本，只需要上面的几个步 骤都是操作新版主进程号，如果要用新版本就上面的几个步骤都操作旧版主进程号就行了。

上面就是Nginx的一些基本的操作，希望以后Nginx能有更好的方法来处理这些操作， 最好是Nginx的命令而不是向Nginx进程发送系统信号。
