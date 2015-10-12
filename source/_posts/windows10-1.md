title: 如何将Windows 10 Build 9926版开始菜单改成之前的可调整大小的菜单
date: 2015-01-26 14:58:57
categories: 技术
tags: windown10
---
Windows 10 Build 9926对之前的开始菜单进行了更改，似乎现在的开始菜单更加方便和实用。

不过你如果想改为之前的可以调整Metro程序大小的那种开始菜单，也可以，只需要导入一个注册表键值即可。

国外开发者@Bavo Luysterborg在Twitter上分享通过修改注册表的方式来找回之前的开始菜单。
<!-- more -->
![d](http://imgcdn.landiannews.com/2015/01/14368-3.jpg)
![](http://imgcdn.landiannews.com/2015/01/14368-1.jpg)
方法如下：

1.开始—运行—输入regedit  （即注册表）

2.依次展开：HKEY_CURRENT_USER---Software----Microsoft---Windows----CurrentVersion----Explorer----Advanced

3.鼠标放到Advanced上右键新建DWORD（32位），命名为EnableXamlStartMenu

4.打开任务管理器–找到Windows 资源管理器并右键重新启动即可，如果你不知道如何重启资源管理器，那么直接重启电脑也可。

为了各位方便使用，鸭子哥已经将该键值单独写入一个注册表文件，你可以点击下面的链接下载，下载后直接打开合并至注册表即可：

Advanced：http://xiazai.landiannews.com/files/2015/01/Advanced.reg

或者，你也可以手动新建一个txt文本，复制粘贴以下内容并保存，将txt改成reg合并到注册表即可：

Windows Registry Editor Version 5.00

[HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Explorer\Advanced]
"EnableXamlStartMenu"=dword:00000000

如果你不想用了，可以再次到达这个路径并删除EnableXamlStartMenu这个键值并重启资源管理器即可。
