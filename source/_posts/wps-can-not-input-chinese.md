title:  关于Ubuntu16.04中WPS不能输入中文的问题 
date: 2016-12-13 12:30:40
categories: 技术
tags: wps
---
### 问题： Ubuntu16.04自带的libre对于office的格式兼容性太差，只好安装了WPS。但是WPS文字、表格、演示均不能输入中文。 
### 原因： 环境变量未正确设置。 
<!-- more -->
### 解决办法:

## WPS文字
 * 打开终端输入：
```sh
	sudo vim /usr/bin/wps
```
* 添加一下文字到打开的文本中（添加到“#!/bin/bash”下面）：
```sh
	export XMODIFIERS="@im=fcitx"
 	export QT_IM_MODULE="fcitx"
```


## WPS表格
 * 打开终端输入：
```sh
        sudo vim /usr/bin/et
```
 * 添加一下文字到打开的文本中（添加到“#!/bin/bash”下面）：
```bash
        export XMODIFIERS="@im=fcitx"
        export QT_IM_MODULE="fcitx"
```

## WPS演示
 * 打开终端输入：
```sh
        sudo vim /usr/bin/wpp
```
 * 添加一下文字到打开的文本中（添加到“#!/bin/bash”下面）：
```sh
        export XMODIFIERS="@im=fcitx"
        export QT_IM_MODULE="fcitx"
```

修改完后保存，打开相应的程序切换输入法就可以输入中文了。
