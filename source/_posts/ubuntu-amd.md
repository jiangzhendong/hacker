title: ubuntu 16.04 amdgpu/radeon开源驱动怎么安装 
date: 2016-11-19 09:53:29
categories: 技术
tags:
 - ubuntu
 - linux
---
AMD发布新版本时fglrx-updates驱动就会自动升级，但其实并不总是这样,因为Ubuntu的源的更新老是会有延迟。所以，你可以自己安装指定版本的专有驱动。
　　准备开始吧，在终端敲入以下命令：
　　``sudo apt-get install build-essential cdbs dh-make dkms execstack dh-modaliases linux-heade``
　　如果你的Ubuntu是64位版本，你还需要这个命令：
　　`sudo apt-get install lib32gcc1`
<!-- more -->
　　这两条命令为下一步创建驱动包部署了必需的环境。
　　然后，去AMD的驱动页面，下载适合你显卡的驱动以及32或是64位的Ubuntu版本。下载完毕后，从 .zip文件中解压出 .run 文件，打开 .run文件的属性并将其设置为可执行。
　　下一步，打开终端，使用 cd 命令进入 .run文件所在文件夹（比如 cd Downloads/ ），然后运行以下命令：
　　`sudo sh ./amd-catalyst-version-here-and-such.run –buildpkg Ubuntu/raring`
　　当然了你得把相应文件名替换进去。如果你使用的不是Ubuntu13.04，你需要用适当的名字替换掉 raring 。

　　当这句命令完毕时你就得到了几个 .deb文件。接下来运行

　　`sudo dpkg -i fglrx*.deb`
　　这个命令会安装所有创建出来的包。完成后，你还得运行一个命令来正确安装新驱动：
　　`sudo amdconfig –initial -f`
　　现在重启电脑，一切搞定！如果你想升级驱动，只要重复以上步骤就好了，从下载驱动开始，到`sudo dpkg -i fglrx*.deb`。
