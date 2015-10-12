title: JAVA 通过SSH访问Windows（或Linux）服务器
date: 2015-01-15 09:53:29
categories: 技术
tags: ssh
---
问题：工作中每次制作版本，需要远程登录到windows桌面去手动执行perl脚本，然后将执行结果取回到本地。来来回回瞎折腾每次都会浪费很多时间。
思考：是否能在本地通过java去访问远程windows客户端，自动执行相应脚本并且自动返回相应结果。
解决思路：开源包Ganymed.jar http://www.cleondris.ch/opensource/ssh2/ 网上已经有很多关于这个开源项目的使用说明了，比如这里，和这里。
<!-- more -->

大笑本人的解决办法：
一、由于是远程连接Windows，所以需要在Windows客户端安装SSH的客户端（FreeSSHd，上文已提到。其他也有很多优秀的SSH客户端，我就不提了）。需要注意的几点：
①FreeSSHd这玩意儿不支持中文，各种乱码。。尼玛。。
②默认端口是21。
③建用户用SHA1建就好，输入用户名密码搞定（为了使用权限，我使用的是NT，域用户登录）。
④本人用Xshell测试与SSH的连接。
二、Java代码：

[java] view plaincopyprint?
package	com.sxy.remote;	 

import java.io.BufferedReader;	
import java.io.IOException;  
import java.io.InputStream;  
import java.io.InputStreamReader;  
import com.sxy.view.MainForm;  
import ch.ethz.ssh2.Connection;	 
import ch.ethz.ssh2.Session;  
import ch.ethz.ssh2.StreamGobbler;  

public class Remote {  

/** 
* @author sxy 2012年11月7日20:17:35 
*/  

private Connection conn=null;  

/** 
* 初始化	
* @param hostName 
* @param hostPort 
*/  
public Remote(String hostName,int hostPort)   
{  
MainForm.getInstance().setConnection(hostName, hostPort);  
conn=MainForm.getInstance().getConnection();  
conn.addConnectionMonitor(new RemoteMonitor(hostName,hostPort));  
}  

/**	
* 连接服务器	
* @param userName 
* @param passWord 
* @return 
* @throws Exception 
*/	
public Boolean connect(String userName,String passWord) throws Exception  
{	  
conn.connect();	 
return conn.authenticateWithPassword(userName, passWord);  
}  

/** 
*执行命令 
* @param conn 
* @param command 
*	@return	
*/  
public static String execComand(Connection conn,String command)	 
{  
StringBuffer response=new StringBuffer();  

if(conn!=null)  
{  
try   
{  
Session sess=conn.openSession();  
sess.execCommand(command);  

InputStream stdout = new StreamGobbler(sess.getStdout());  
@SuppressWarnings("resource")	 
BufferedReader br = new BufferedReader(new InputStreamReader(stdout));  

while(true)  
{  
String line	= br.readLine();  
if(line==null)	 
{  
break;  
}	 
response.append(line+"\n");	 
}	  

Integer execResult=sess.getExitStatus();  
System.out.println(execResult==null?"执行结果：Null":(execResult.intValue()==0?"执行结果：成功":"执行结果：失败"));  
sess.close();	 
}	  
catch	(IOException e)	 
{	 
e.printStackTrace();  
}		
}  

return response.toString();	 
}  
}  
三、调用：
[java] view plaincopyprint?
package	com.sxy.test;
import com.sxy.remote.Remote;
import com.sxy.view.BaseFrame;
import com.sxy.view.MainForm;
public class TestConnection {
/** 
* 测试 
* @param args 
*/  
public static void main(String[] args)	 
{  
BaseFrame bf=MainForm.getInstance();  

try	 
{		  
String hostName="127.0.0.1";  
int	port=222;  
String userName="sxy";	
String passWord="jjj";  

if(new Remote(hostName,port).connect(userName, passWord))  
{  
System.out.println("登录成功啦");  
}  
else  
{	 
System.out.println("登录失败");	
}  

String command="C:\\Users\\sxy\\Desktop\\test.bat 参数1	参数2";	  
String response=Remote.execComand(bf.getConnection(), command);  
System.out.println(response);  

bf.getConnection().close();	 
}	
catch (Exception e)	  
{  
e.printStackTrace();  
}	  
}  
}
其实还可以调用远程端的一个.class文件，通过该文件去执行bat文件。
[java] view plaincopyprint?
String command=”java -classpath	.;C:\Users\sxy\Desktop Test 参数1 参数2”;
Test.class：
[java] view plaincopyprint?
import java.io.IOException;
public class Test {
/** 
* @param	args 
*/	
public static void main(String[] args) {  
// TODO	Auto-generated method stub  

//System.out.println("参数1:"+args[0]+"参数2:"+args[1]);  

String tesingBatPath="C:\\Users\\sxy\\Desktop\\123.BAT "+args[0]+"	"+args[1];  
try   
{	
Runtime.getRuntime().exec("cmd	/c start "+tesingBatPath);  
System.out.println(tesingBatPath+"	  执行成功");  
}	 
catch (IOException	e1)  
{	
e1.printStackTrace();	
}  

}  
}
