title: perl语言入门学习笔记
date: 2014-12-25 09:53:29
categories: 技术
tags: perl
---
 perl内部并不存在整数，在perl内部总是按照双精度浮点数的要求来保存数字并进行运算的。
### 如果要使perl支持unicode字符，需要添加 use utf8;
\l 将下一个字母转换为小写
\L 将它后面的所有字母都换为小写，直到\E为止
\u 将下一个字母转换为大写<!-- more -->
\U 将它后面的所有字母都换为大写，直到\E为止
\Q 相当于把它到\E之间的非单词字符加上反斜杠
\E 结束\L、\U、\Q开始的作用范围
### per的告警信息：
```js
	use warnings;
```
或者使用 perl -w运行脚本
或者加上 `#!/usr/bin/perl -w`
如果看不懂告警，就是用
```js
use diagnostics;
```
### 打印更详细的信息
```js
$#aaa表示aaa数组的最后一个元素的索引值
```
关于perl中布尔值的判断
* 1、如果是数字，0为假；所有其他的为真
* 2、如果是字符串，空字符串为假，所有其他的字符串为真
* 3、如果既不是数字也不是字符串，那就先换成数字或者字符串再进行判断。
### 使用defined函数来判断变量是否定义
#### 数组定义：
```js
my @p1=(‘aaa’,’bbb’,’ccc’);
my @p2=(1..100);
my @p3=qw(aaa bbb ccc);
my @p4=qw! aaa bbb ccc !;
$end=$#p1;
$number=$#p1+1;
$last=$p1[$#p1];
$last=$p1[-1];
```
pop取出数组中的最后的一个元素并将其作为返回值返回。
```js
$last=pop(@p1);
```
push添加元素到数组的最后
```js
push(@p1,’jaj’);
push @p1,’aaaa’;
push @p1,@p2;
```
shift取出数字第一个元素并将其作为返回值返回
```js
$first=shift(@p1);
```
unshift添加元素到数组的最左边
```js
unshift(@p1,’jaj’);
unshift @p1,@p2;
```
splice可以实现从中间操作数组；
foreach 来循环遍历数组中的值
reverse反转数组
sort 对数组进行排序
sort(@p1) 正向排序
reverse sort(@p1) 逆向排序
chomp(@p1);
chomp(@line =);
可以使用state操作符来声明持久性私有变量
state $sum=0;
state @numbers;
打印数组：
print @array
print “@array”
@ARGV是存放所有参数的数组
<>砖石操作符，把输入参数当作文件处理
文件操作符（文件句柄）
```js
open CONFIG, ‘test’;
open CONFIG, ‘<test’;
open CONFIG, ‘>test’;
open CONFIG, ‘>>test’;
```
关闭文件句柄：
close CONFIG;
读取或者写入的时候指定文件的字符编码
open CONFIG, ‘<:encoding(UTF-8)’, ‘test’;
open CONFIG, ‘>>:encoding(UTF-8)’, ‘test’;
打印perl能处理的字符编码清单
```js
perl -MEncode -le “print for Encode->encodings(‘:all’)”
```
判断文件句柄是否成功的代码：
```js
my $success=open CONFIG, ‘>>’, test’;
if( !$success ){ print “create file faild”;
}
if( ! open CONFIG, ‘>>’, ‘test’ ){
die “Can’t create config file: $!”;
}
if( @ARGV<2 ){
die “Not enough arguments\n”;
}
```
自动检查致命错误
use autodie;
可以将文件句柄设置成变量，这样文件句柄可以作为子程序的参数传递，或者放在数组和hash中排序，或者严格控制其作用域。
打印往文件句柄中写入数据的时候不需要加入逗号！
```js
hash
申明hash
my %test=(‘foo’=>35,’bar’=>39,’carl’=>34,’zhang’=>89);
my %test=(‘foo’,35,’bar’,39,’carl’,34,’zhang’,89);
访问哈希元素
$hash{$key};
哈希元素赋值
$hash{‘test’}=’carlzhang’;
访问整个hash
my @any_array=%array;
print “@any_array\n”;
hash转换
my %test_hash=reverse %hash;
hash函数
keys函数能返回hash的所有键列表
values函数能返回hash所有的值列表
my %test=(‘foo’=>35,’bar’=>39,’carl’=>34,’zhang’=>89);
my @test_key=keys%test;
my @test_value=values%test;
print “test_key is: @test_key\n”;
print “test_value is: @test_value\n”;
使用each函数遍历hash
my %test=(‘foo’=>35,’bar’=>39,’carl’=>34,’zhang’=>89);
while( my($test_key,$test_value)=each %test){
print “$test_key=>$test_value\n”;
}
my %test=(‘foo’=>35,’bar’=>39,’carl’=>34,’zhang’=>89);
foreach my $test_key (sort keys %test){
my $test_value=$test{$test_key};
print “$test_key=>$test_value\n”;
}
判断键值是否存在：
if ( exists $test{‘foo’}){
print “key fool is in test hash\n”;
}
删除hash中的某个元素
delete $test{‘foo’};
%ENV环境变量
my @test=%ENV;
foreach $test_key (keys %ENV){
print “$test_key=>$ENV{$test_key}\n”;
}
```
模式匹配：
m/string/;
m%string%;
/string/;
使用/i忽略带小写
/string/i
用/s匹配任意字符
/test.aaa/s 会匹配test和aaa之间的所有内容，包括换行符
用/x加入空白符，有了/x，perl会忽略空格和正则表达式中的perl风格的注释。
\A表示字符串开头
\z表示要匹配的字符串绝对末尾
\b匹配单词边界
\B不匹配单词边界
捕获
使用?:来指定不捕获小括号内的东东
使用?来命名捕获的内容，使用$+{LABEL}来提取捕获的内容
贪婪匹配 ? +? {1,}? {4,20}? ??
$^I 读取文件后对文件进行备份
perl模块
perldoc CGI 查看模块的用法
cpan -a 列出安装的模块
模块的安装：
perl Makefile.PL
make
make install
可以在perl Makefile.PL后面通过`INSTALL_BASE`指定安装的路径
File::Basename和File::Spec的使用
```js
use File::Basename;
use File::Spec;
my $name=”/usr/local/bin/perl”;
my $filename=basename $name;
my $dirname=dirname $name;
my $new_name=File::Spec->catfile($dirname,$filename);
print “filename is $filename\n”;
print “dirname is $dirname\n”;
print “newname is $new_name\n”;
```
文件操作
文件测试操作符
```js
-r 文件或者目录对当前用户或组来说是可读的
-w 文件或者目录对当前用户或组来说是可写的
-x 文件或者目录对当前用户或组来说是可执行的
-o 文件或目录由当前用户拥有
-R 文件或者目录对实际的用户或者组是可读的
-W 文件或者目录对实际的用户或者组是可写的
-X 文件或者目录对实际的用户或者组是可执行的
-O 文件或者目录由实际的用户拥有
-e 文件或者目录是存在的
-z 文件存在且没有内容（对目录来说永远为假）
-s 文件或者目录存在而且有内容（返回值是以字节为单位的文件大小）
-f 是普通文件
-d 是目录
-l 是符号链接
-S 是socket类型的文件
-p 是命名管道，也就是先入先出(fifo)队列
-b 是块设备文件（比如某个可挂载的磁盘）
-c 是字符设备文件（比如某个I/O设备）
-u 文件或者目录设置了setuid位
-g 文件或者目录设置了setgid位
-k 文件或者目录设置了sticky位
-t 文件句柄是TTY设备
-T 看起来像文本文件
-B 看起来像二进制文件
-M 最后一次被修改后至今的天数
-A 最后一次被访问后至今的天数
-C 最后一次文件节点编号(inode)被变更后至今的天数
```
测试统一文件的多项属性
```js
if (-r $file and -w $file){
…..
}
```
可以改写成如下语句，以提高性能
```js
if (-r $file and -w ){
…..
}
```
符号表示虚拟文件句柄，它会告诉perl用上次查询过的文件来做当前测试。
```js
stat和lstat函数
stat函数返回一个有13个元素组成的列表。
dev 0 文件所在的设备编号
ino 1 文件的inode标号
mode 2 文件模式（权限、类型）
nlink 3 文件或者目录的连接数
uid 4 文件的用户ID
gid 5 文件的组ID
rdev 6 设备识别码（只用于特殊文件）
size 7 文件总的自己数
atime 8 文件最后访问时间
mtime 9 文件最后更改时间
ctime 10 inode更改的时间
blksize 11 文件系统I/O首选块的大小
blocks 12 实际分配的文件块数
localtime函数
($sec,$min,$hour,$mday,$mon,$year_off,$wday,$yday,$isdat) = localtime;
$sec 秒，0 ~ 59
$min 分，0 ~ 59
$hour 时，0 ~ 23
$mday 月份中的日期， 1 ~ 2 8、2 9、3 0或3 1
$mon 年份中的月份， 0 ~ 11（这里请特别要小心）
$year_off 1900年以来的年份。将1900加上这个数字，得出正确的4位数年份
$wday 星期几，0 ~ 6
$yday 一年中的第几天，0 ~ 364或365
$isdst 如果夏令时有效，则为真
perl目录操作
chdir改变工作目录
chdir ‘/etc’ or die “can’t chdir to /etc: $!\n”;
```
在程序内部使用通配符匹配文件
```js
my @files=glob ‘. ‘; #匹配多个模式需要用空格隔开，也可以采用my @files=<*>;的方式
print “@files\n”;
opendir 打开目录
readdir 读取目录下的文件名
closedir 关闭打卡的目录句柄
测试代码：
use File::Spec::Functions;
my $dir=’/etc’;
opendir DIR,$dir or die “can’t open /etc directory: $!\n”;
foreach my $file (readdir DIR){
if ($file=~/^./){
next;
}
$file=catfile($dir,$file);
print “we found $file in /etc directory\n”;
}
closedir DIR;
unlink删除文件，返回成功删除文件的数目，unlink不能用来删除目录
rename重命名文件
测试代码：
for my $file (glob ‘*.old’){
my $new_file=$file;
$new_file=~s/old/new/;
if (-z $new_file){
print “$new_file is exists ,please check it out\n”;
}else{
rename $file,$new_file or warn “rename $file faild,please check it out\n”;
print “rename $file success ,don’t worry\n”;
}
}
```
创建和删除目录
```js
mkdir 创建目录
my $dir=’aaa’;
my $permissions=”0755”;
mkdir ‘aaa’,oct($permissions) or die “can’t create directory aaa: $!\n”;
rmdir删除目录，每次只能删除一个目录，而且删除时目录必须为空，不然会导致失败!
chmod修改文件或者目录权限
chmod 0755,’test’;
chown修改文件或者目录的属主或者属组,返回受影响的文件数量
测试代码:
my $user=52;
my $group=52;
chown $user,$group,’bbb’;
defined(my $user1=getpwnam ‘puppet’) or die “bad user: $!\n”;
defined(my $group1=getpwnam ‘puppet’) or die ‘bad group: $!\n’;
chown $user1,$group1,’bbb.new’;
utime修改文件的时间戳(最近的更改和访问时间)
测试代码：
my $now=time;
my $ago=$now-246060;
utime $now,$ago,’bbb’;
字符串和排序
用index查找子字符串
my $string='hah aaa bbbcc aa ddd';
my $part=index($string,'aa');
print "$part\n";
```
排序
按照数字排序<=>;
按照字符排序cmp;
```js
my @some_numbers=qw{1 10 23 100 34 45};
my @some_chars=qw{aa cc bb dd dc bc};
sub by_number{
$a<=>$b;
}
sub by_char{
$a cmp $b;
}
my @test=sort by_number @some_numbers;
my @test2=sort by_char @some_chars;
print “@test\n”;
print “@test2\n”;
sub sort_test{
substr($test1{$a},2,6) <=> substr($test1{$b},2,6)
or substr($test1{$a},0,1) cmp substr($test1{$b},0,1)
}
```
执行外部命令
IPC::System::Simple
system systemx
capture capturex
错误扑捉：
```js
eval
my $aaa1=$ARGV[0];
my $aaa2=$ARGV[1];
my $test=eval{$aaa1/$aaa2} || ‘aaa’;
print “I couldn’t divide by \$aaa2: $@” if $@;
print “$test”;
```
有4中类型的错误eval捕捉不到：
* 1、代码语法错误
* 2、perl解析器本省的崩溃错误
* 3、告警类错误
* 4、每次调用exit的时候
