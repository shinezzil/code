<?php

/* 
 * 将JSON字符串或PHP数组逐行解析
 * @author  zhouq
 * @date    2015-07-06
 */

class ParseJSON{
    
    //行数组，用于保存解析结果
    private static $content = array();
    
    //样式
    private static $lineStyleArray  = array();
    
    //单位缩进数量
    private static $fixStr  = "&nbsp;&nbsp;&nbsp;&nbsp;";
    
    //递归层级统计
    private static $total   = 0;
    
    /**
     * 样式配置
     */
    private static function setConfigArray($style = 1){
        $styleConfig    = array(
            '1' => array(
                'keyStyle'      => 'line-height:24px; font-family: monospace; font-weight:bold;',//键的样式
                'valueStyle'    => 'line-height:24px; font-family: monospace; color:#7da2ec;',//值的样式
                'default'       => array(
                    'number'    => 'line-height:24px; font-family: monospace; color:#7da2ec;',//数字
                    'string'    => 'line-height:24px; font-family: monospace; color:#7da2ec;',//字符串
                    'bool'      => 'line-height:24px; font-family: monospace; color:#7da2ec;',//布尔
                ),
            ),
            '2' => array(
                'keyStyle'      => 'line-height:24px; font-family: monospace;',//键的样式
                'valueStyle'    => 'line-height:24px; font-family: monospace; color:#58d81d;',//值的样式
                'default'       => array(
                    'number'    => 'line-height:24px; font-family: monospace; color:#ec1010;',//数字
                    'string'    => 'line-height:24px; font-family: monospace; color:#58d81d;',//字符串
                    'bool'      => 'line-height:24px; font-family: monospace; color:#eab733;',//布尔
                ),
            ),
        );
        
        if(!isset($styleConfig[$style])){
            $style  = 1;
        }
        
        self::$lineStyleArray   = $styleConfig[$style];
        
        return true;
    }


    /**
     * 对外调用接口
     * @params data json字符串或数组
     * @params style 样式，可在config中自定义
     */
    public static function getJsonArray($data, $style=2){
        //源数据
        $jsonArray      = array();
        
        //判断数据格式
        $result['flag'] = 0;
        if(is_string($data)){
            if(!Helper_Global_Func::isJsonString($data)){
                $result['msg']  = "数据源不是合法的JSON字符串";
                return $result;
            }
            $jsonArray  = api_json_decode($data);
        }
        if(is_array($data)){
            $jsonArray  = $data;
        }
        
        //判断数据
        if(!is_array($jsonArray) || empty($jsonArray)){
            $result['msg']      = "数据源为空或错误";
            return $result;
        }
        
        //设置样式
        self::setConfigArray($style);
        
        //调用格式化
        self::formatJson($jsonArray);
        
        //返回信息
        $result['flag'] = 1;
        $result['info'] = self::$content;
        
        return $result;
    }
    
    /**
     * 判断数组是否为关联数组
     */
    private static function isAssocArray($array = array()){
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    
    /**
     * 递归调用JSON格式化函数
     * @params array 要处理的数组  
     * @params last 是否最后一个元素，用于判断结束符，默认不用传值 1是 0否
     */
    private static function formatJson($array, $last=1){
        self::$total++;
        
        //缩进字符
        $nbspStr        = $newNbspStr = '';
        for($i=1; $i<=self::$total; $i++){
            $newNbspStr .= self::$fixStr;
        }
        
        //数组类型判断 0索引 1关联
        $assoc          = 0;
        $flag           = self::isAssocArray($array);
        if($flag){
            $assoc      = 1;
        }
        
        //数组对象的前缀
        $fixStartSign       = "[";
        $fixEndSign         = "]";
        if($assoc == 1){
            $fixStartSign   = "{";
            $fixEndSign     = "}";
        }
        
        //将数组开头保存
        self::$content[]    = "{$fixStartSign}<br/>";
        
        if(is_array($array)){
            
            //键值样式
            $keyStyle           = self::$lineStyleArray['keyStyle'];
            $valueStyle         = self::$lineStyleArray['valueStyle'];
            
            //计数参数
            $curTimes   = 1;//循环次数
            $valTotal   = count($array);
            
            foreach($array as $k=>$v){
                
                //判断当前元素是否为最后一个
                $curLast        = 0;
                $lastFix        = ",";
                if($curTimes == $valTotal){
                    $curLast    = 1;
                    $lastFix    = "";
                }
                $curTimes++;
                
                //当前键值为数组时，递归处理
                if(is_array($v)){
                    if($assoc == 1){
                        self::$content[]    = "{$newNbspStr}<span style='{$keyStyle}'>{$k}:</span> ";
                    }else{
                        self::$content[]    = "{$newNbspStr}";
                    }
                    self::$content[]    = self::formatJson($v,$curLast);
                }else{
                    //值类型的判断
                    $vStr           = '"'.$v.'"';
                    
                    //个性化样式
                    if(isset(self::$lineStyleArray['default'])){
                        if(is_bool($v)){//布尔
                            $valueStyle = self::$lineStyleArray['default']['bool'];
                            if($v){
                                $vStr   = 'true';
                            }else{
                                $vStr   = 'false';
                            }
                        }else if(is_numeric($v)){//数字
                            $valueStyle = self::$lineStyleArray['default']['number'];
                            $vStr   = $v;
                        }else{//其它及字符串
                            $valueStyle = self::$lineStyleArray['default']['string'];
                        }
                    }
                    
                    //内容
                    if($assoc == 1){
                        self::$content[]    = "{$newNbspStr}<span style='{$keyStyle}'>{$k}:</span> <span style='{$valueStyle}'>{$vStr}</span>{$lastFix}<br/>";
                    }else{
                        self::$content[]    = "{$newNbspStr}<span style='{$valueStyle}'>{$vStr}</span>{$lastFix}<br/>";
                    }
                }
                
            }
        }  
        
        //每回退一层，total减1，保持前缀空格对齐
        self::$total--;
        for($i=1; $i<=self::$total; $i++){
            $nbspStr    .= self::$fixStr;
        }
        
        if($last){
            self::$content[]    = "{$nbspStr}{$fixEndSign}<br/>";
        }else{
            self::$content[]    = "{$nbspStr}{$fixEndSign},<br/>";
        }
    }
    
    
}