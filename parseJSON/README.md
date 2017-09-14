# json数据可视化解析

![Alt text](http://oph42y401.bkt.clouddn.com/Upload-Image-20170914120734_420.png)


# 使用示例

```php
include ParseJSON.php

$testArray  = array(
    'zhangsan'  => array(
        'name'  => 'zhangsan',
        'sex'   => 1,
        'isSuccess' => false,
    ),
    'lisi'  => array(
        'name'  => 'lisi',
        'sex'   => 2,
        'age'   => 23,
    ),
);

$result     = ParseJSON::getJsonArray($testArray);
if($result['flag']){
    foreach($result['info'] as $row){
        echo $row;
    }
}
exit;

```


# 问题与咨询

个人网址:http://www.noomall.cn

咨询QQ:281-818-570
