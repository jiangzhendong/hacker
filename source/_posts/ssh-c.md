title: SSH框架总结（框架分析+环境搭建+实例源码下载）
date: 2015-02-06 14:58:57
categories: 技术
tag: ssh
----
首先，SSH不是一个框架，而是多个框架（struts+spring+hibernate）的集成，是目前较流行的一种Web应用程序开源集成框架，用于构建灵活、易于扩展的多层Web应用程序。
 
集成SSH框架的系统从职责上分为四层：表示层、业务逻辑层、数据持久层和域模块层（实体层）。
 
Struts作为系统的整体基础架构，负责MVC的分离，在Struts框架的模型部分，控制业务跳转，利用Hibernate框架对持久层提供支持。Spring一方面作为一个轻量级的IoC容器，负责查找、定位、创建和管理对象及对象之间的依赖关系，另一方面能使Struts和Hibernate更好地工作。
<!-- more -->
![](http://img.my.csdn.net/uploads/201304/15/1366010139_2255.png)
由SSH构建系统的基本业务流程是：
1、在表示层中，首先通过JSP页面实现交互界面，负责传送请求(Request)和接收响应(Response)，然后Struts根据配置文件(struts-config.xml)将ActionServlet接收到的Request委派给相应的Action处理。
2、在业务层中，管理服务组件的Spring IoC容器负责向Action提供业务模型(Model)组件和该组件的协作对象数据处理(DAO)组件完成业务逻辑，并提供事务处理、缓冲池等容器组件以提升系统性能和保证数据的完整性。
3、在持久层中，则依赖于Hibernate的对象化映射和数据库交互，处理DAO组件请求的数据，并返回处理结果。
 
采用上述开发模型，不仅实现了视图、控制器与模型的彻底分离，而且还实现了业务逻辑层与持久层的分离。这样无论前端如何变化，模型层只需很少的改动，并且数据库的变化也不会对前端有所影响，大大提高了系统的可复用性。而且由于不同层之间耦合度小，有利于团队成员并行工作，大大提高了开发效率。
 
 
下面我们再详细看一下组成SSH的这三个框架
一、Spring
1、什么是Spring？
简单来说，Spring是一个轻量级的控制反转（IoC）和面向切面（AOP）的容器框架。
 
2、Spring的特性
![](http://img.my.csdn.net/uploads/201304/15/1366009800_5913.png)
具体自己百度吧
所有Spring的这些特征使你能够编写更干净、更可管理、并且更易于测试的代码
 
3、为什么使用Spring？
Spring的以上特性使得开发人员使用基本的JavaBean来完成以前只可能由EJB完成的事情。然而，Spring的用途不仅限于服务器端的开发。从简单性、可测试性和松耦合的角度而言，任何Java应用都可以从Spring中受益。
 
二、Struts
1、什么是Struts？
它通过采用 Java Servlet/JSP 技术，实现了基于Java EEWeb应用的MVC设计模式的应用框架，是MVC经典设计模式中的一个经典产品。
 
2、Struts1的核心构成
![](http://img.my.csdn.net/uploads/201304/15/1366011574_3130.png)
在 Struts1 中，由一个名为 ActionServlet 的 Servlet 充当 控制器(Controller)的角色，根据描述模型、视图、控制器对应关系的 struts-config.xml 的配置文件，转发视图(View)的请求，组装响应数据模型（Model）。
 
在 MVC 的模型（Model）部分，经常划分为两个主要子系统（系统的内部数据状态与改变数据状态的逻辑动作），这两个概念子系统分别具体对应 Struts 1里的 ActionForm 与 Action 两个需要继承实现超类。在这里，Struts 1可以与各种标准的数据访问技术结合在一起，包括Enterprise Java Beans（EJB）, JDBC 与 JNDI。
 
在 Struts 1的视图（View） 端，除了使用标准的JavaServer Pages（JSP）以外，还提供了大量的标签库使用，同时也可以与其他表现层组件技术（产品）进行整合，比如 Velocity Templates，XSLT 等。
 
通过应用 Struts 的框架，最终用户可以把大部分的关注点放在自己的业务逻辑（Action）与 映射关系的配置文件（struts-config.xml）中。
 
3、Struts1的基本执行流程
详见《STRUTS基本工作流程》
 
4、什么是struts2
Struts 2是Struts的下一代产品，是在 struts 1和WebWork的技术基础上进行了合并的全新的Struts 2框架。其全新的Struts 2的体系结构与Struts 1的体系结构差别巨大。Struts 2以WebWork为核心，采用拦截器的机制来处理用户的请求，这样的设计也使得业务逻辑控制器能够与ServletAPI完全脱离开，所以Struts 2可以理解为WebWork的更新产品
 
5、strut2的体系结构
![]()
