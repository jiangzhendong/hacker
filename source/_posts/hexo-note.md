title: 学习如何实现hexo博客文档
date: 2014-12-26 12:30:40
categories: 技术
tags: hexo
---
##学习ejs网页模板文件编写
###关于ejs
是一个JavaScript模板库，用来从JSON数据中生成HTML字符串。EJS在这里专指WEB所使用的模板引擎之一。EJS的优点是将会带给你明确、维护性良好的HTML代码结构。<!-- more -->
###学习ejs
####安装 EJS 命令如下：
    npm install ejs
####jhon的代码
```js
// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
  (function(){
  var cache = {};
 
  this.tmpl = function tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :
     
      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +
       
        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +
       
        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");
   
    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();
```
##学习yalm配置
###学习写yalm的语法



##学习编写hexo
###学习markdown编写文章


