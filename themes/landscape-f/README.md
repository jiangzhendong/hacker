# Landscape-F

A theme based on [Hexo]'s default theme [Landscape].

- [Preview](http://howiefh.github.io/hexo-theme-landscape-f/)
- [My Blog](http://howiefh.github.io/2014/04/20/hexo-optimize-and-my-theme-landscape-f/)

## Installation

### Install

``` bash
$ git clone https://github.com/howiefh/hexo-theme-landscape-f.git themes/landscape-f
```

**Landscape-F requires Hexo 2.4 and above.**

### Enable

Modify `theme` setting in `_config.yml` to `landscape-f`.

### Update

``` bash
cd themes/landscape-f
git pull
```

## Configuration

``` yml
# Header
menu:
  Home: /
  Archives: /archives
rss: /atom.xml
github: https://github.com/howiefh

# Content
excerpt_link: Read More
fancybox: true

# Sidebar
sidebar: right
widgets:
- about_me
- category
- tag
- tagcloud
- archive
- calendar
- recent_posts
- duoshuo_recent_comments
- links

# Miscellaneous
google_analytics:
favicon: /favicon.ico
twitter:
google_plus:
fb_admins:
fb_app_id:

# Toc
toc:
  article: true   ## show contents in article.
  aside: true     ## show contents in aside.

# Scroll to top
go_top: true

# duoshuo
duoshuo_shortname: 

# baidu share
baidushare: true

# blogroll
links:
- name: 404 page
  link: http://yibo.iyiyun.com/js/yibo404/key/1

# about me
about_me:
  title: ABOUT ME
  gravatar: a@abc.com
  avatar: /images/github.jpg
  texts:
  - Hi,I'm howiefh.

# display updated
display_updated: true
```

- **menu** - Navigation menu
- **rss** - RSS link
- **github** - Github link
- **excerpt_link** - "Read More" link at the bottom of excerpted articles. `false` to hide the link.
- **fancybox** - Enable [Fancybox]
- **sidebar** - Sidebar style. You can choose `left`, `right`, `bottom` or `false`.
- **widgets** - Widgets displaying in sidebar
- **google_analytics** - Google Analytics ID
- **favicon** - Favicon path
- **twitter** - Twiiter ID
- **google_plus** - Google+ ID
- **toc** - Show toc in article or sidebar
- **go_top** - Go to top
- **duoshuo_shortname** - Duoshuo shortname
- **baidushare** - Show baidu share in post
- **links** - Links displaying in sidebar
- **about_me** - About me displaying in sidebar. The gravatar is your gravatar e-mail. You can choose gravatar or avatar to show your picture. 
- **display_updated** - Display updated in article footer

## Features

### Fancybox

Landscape-F uses [Fancybox] to showcase your photos. You can use Markdown syntax or fancybox tag plugin to add your photos.

```
![img caption](img url)

{% fancybox img_url [img_thumbnail] [img_caption] %}
```

### Sidebar

You can put your sidebar in left side, right side or bottom of your site by editing `sidebar` setting.

Landscape-F provides 9 built-in widgets:

- category
- tag
- tagcloud
- archive
- recent_posts
- about_me
- calendar
- duoshuo_recent_comments
- links

All of them are enabled by default. You can edit them in `widget` setting.

## Development

### Requirements

- [Grunt] 0.4+
- Hexo 2.4+

### Grunt tasks

- **default** - Download [Fancybox] and [Font Awesome].
- **fontawesome** - Only download [Font Awesome].
- **fancybox** - Only download [Fancybox].
- **clean** - Clean temporarily files and downloaded files.

[Hexo]: http://zespia.tw/hexo/
[Fancybox]: http://fancyapps.com/fancybox/
[Font Awesome]: http://fontawesome.io/
[Grunt]: http://gruntjs.com/
[Landscape]: https://github.com/hexojs/hexo-theme-landscape
