### 项目简介

- 货运ERP API

### 目标管理后台

http://47.98.190.163:86/admin/index
zhangqi
51985198

### 文档信息

【金山文档 | WPS云文档】 货运系统开发

### 扩展包

- [操作日志] https://spatie.be/docs/laravel-activitylog/v4/introduction
- [权限] https://spatie.be/docs/laravel-permission/v6/introduction]

### 文档

【金山文档 | WPS云文档】 货运系统开发
https://www.kdocs.cn/l/cugZmeE4Azjj

### 宝塔修改php版本

* rm -f /usr/bin/php
* ln -sf /www/server/php/72/bin/php /usr/bin/php

### TODO

https://mp.weixin.qq.com/s/fl7MNQLjD-YtROcMprFa1w

### 计算公式

应收人民币-特殊费用人民币-应付人民币=毛利人民币
应收美金-应付美金=毛利美金
总利润=毛利人民币+毛利美金*当月汇率

商务建立单子后 单子会出现在商务--商务列表中（单子就被分为两种，一种是已认领的，一种是未被认领的）

建单的时候选了操作的名字 那这个单子会发送到操作--商务列表中

操作通过操作--商务列表进行操作（这个动作被命名为认领），然后操作--商务列表中被认领的单子去了操作单据中（也就是说操作--商务单据中剩下是未被认领的）

然后被认领的单子进入到操作单据后，操作会在这里进行操作，并且同步到商务--商务列表的单据中

ssh-copy-id root@124.222.232.138
KxGPHA1fmA5LNJyiqbfm
