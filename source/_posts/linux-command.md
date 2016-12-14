title: linux学习心得
date: 2016-09-14 09:53:29
categories: 技术
tags: [linux,lsof]
---
## linux文件删除，空间不释放
文件系统使用率100%，然后删除了一个80G的文件，但是使用`df -k`查看，文件系统使用率还是100%
### 工具/原料 
* lsof
### 方法/步骤
初步判读是删除文件时有进程在使用文件，导致空间未释放。使用`lsof | grep delete` 查找到相应的进程号，然后用`kill -9 删掉进程`，在次使用`df -k`检查文件系统，发现可用空间为80G .
<!-- more -->
## Linux 命令
* 立即让网络支持nat : `echo 1 | sudo tee /proc/sys/net/ipv4/ip_forward; sudo iptables -t nat -I POSTROUTING -j MASQUERADE`
* 按cpu利用率从大到小排列进程:  `ps -eo "%C : %p : %z : %a"|sort -nr`
* 显示包含字符串的文件名:  `grep -l -r 字符串 路径`
* 统计80端口的连接并排序 :  `netstat -na|grep :80|awk '{print $5}'|awk -F: '{print $1}'|sort|uniq -c|sort -r -n`
* 显示当前内存大小:  `free -m |grep "Mem" | awk '{print $2}'`
* 统计每个单词的出现频率并排序 :  `awk '{arr[$1]+=1 }END{for(i in arr){print arr[i]"\t"i}}' FILE_NAME | sort -rn`
* 查看CPU信息:  `cat /proc/cpuinfo`
* 手工增加一条路由 :  sudo route add -net 192.168.0.0 netmask 255.255.255.0 gw 172.16.0.1
* 控制台下显示中文 :  sudo apt-get install zhcon;zhcon --utf8 --drv=vga
* 把终端加到右键菜单:  sudo apt-get install nautilus-open-terminal
* 显示一小时以内的包含 xxxx 的文件:  find . -type f -mmin -60|xargs -i grep -l xxxx '{}'
* 手工删除一条路由:  sudo route del -net 192.168.0.0 netmask 255.255.255.0 gw 172.16.0.1
* 统计程序的内存耗用:  ps -eo fname,rss|awk '{arr[$1]+=$2} END {for (i in arr) {print i,arr[i]}}'|sort -k2 -nr
* vim 如何显示行号:  :set number
* 显示最近2小时到8小时之内的文件:  find . -mmin +120 -mmin -480 -exec more {} \;
* 命令关机:  sudo halt
* 查看网络连接状态:  netstat -n | awk '/^tcp/ {++S[$NF]} END {for(a in S) print a, S[a]}'
