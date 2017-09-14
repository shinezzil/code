# json数据可视化解析

显示效果

![Alt text](http://7jpsyo.com1.z0.glb.clouddn.com/9DAB5513-E5B0-45DD-8261-2D571891A7A2.png)


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
