<?php

class Base_Message {

    private function __construct() {}

    /**
     * 正确信息提示，包括页面或api，错误号code为0
     * @param string $msg 信息提示, 输出接口中：$data['status']['msg'];
     * @param array $data 数据内容, 输出接口中：$data['data'];
     * @param array $otherData 扩展数据内容, 输出接口中：$data['xxx']; 与以上data节点同级
     * @param string $url 如果是页面提示形式，提示完成后跳转的url
     * @param int $t 提示完成后调整的等待时间，默认为3秒
     * @param string $ie 数据内容编码，默认支持为utf-8
     * @param string $oe 输出内容编码，默认为utf-8（json结果输出时设置无效,统一为unicode）
     */
    public static function showSucc($msg, $data=array(), $otherData=array(), $url='', $t=3, $ie='', $oe='UTF-8') {
        self::message(0, $msg, $data, $url, $t, $otherData, $ie, $oe);
    }


    /**
     * 错误信息提示，包括页面或api
     * @param string $msg 信息提示, 输出接口中：$data['status']['msg'];
     * @param array $data 数据内容, 输出接口中：$data['data'];
     * @param int $code 错误号，默认为11
     * @param string $url 如果是页面提示形式，提示完成后跳转的url
     * @param int $t 提示完成后调整的等待时间，默认为3秒
     * @param string $ie 数据内容编码，默认支持为utf-8
     * @param string $oe 输出内容编码，默认为utf-8（json结果输出时设置无效,统一为unicode）
     */
    public static function showError($msg, $data=array(), $code=11, $url='', $t=3, $ie='', $oe='UTF-8') {
        self::message($code, $msg, $data, $url, $t, array(), $ie, $oe);
    }

    /**
     * 输出json/xml/html格式的消息。该函数参数很多，还读取$_REQUSET的format、fileds参数，很凶残呐
     * @param int $code 错误号， 0表示没有错误发生
     * @param string $msg 结果描述
     * @param array $data 数据，可以是一维数组，也可以是二维数组， 仅在输出json/xml数据时有用
     * @param string $url 将要跳转的页面，仅在输出html页面时使用
     * @param int $t 跳转等待时间，仅在输出html页面时使用
     * @param array $otherData 消息的补充字段， 仅在输出json/xml数据时有用
     * @param string $ie 输入数据的编码，默认为gbk
     * @param string $oe 输出数据的编码，默认为utf8
     */
    protected static function message($code, $msg, $data, $url = 'http://www.chope.co', $t, $otherData=array(), $ie='', $oe='UTF-8') {
        $format = empty($_REQUEST['format']) ? 'json' : strtolower($_REQUEST['format']);
        if (isset($_GET['oe']) && in_array(strtoupper($_GET['oe']), array('GBK', 'UTF-8'), true)) {
            $oe = $_GET['oe'];
        }
        $oe = $format === 'json' ? 'UTF-8' : $oe;// 标准的json只支持utf8中文
        $code = intval($code);
        // 转码
        if(!empty($ie) && strcasecmp($ie, $oe) !== 0) {
            $msg = Base_Common::convertEncoding($msg, $oe, $ie);
            $data = Base_Common::convertEncoding($data, $oe, $ie);
            $otherData = Base_Common::convertEncoding($otherData, $oe, $ie);
        }

        // 依据不同格式选择性输出
        switch($format) {
            case 'xml':
                header("Content-Type: text/xml; charset=" . strtoupper($oe));
                $outArr = array();
                if (!is_array($msg)) {
                    $outArr['status']['code'] = $code;
                    $outArr['status']['msg'] = $msg;
                    if (is_array($otherData)) {
                        foreach ($otherData as $k=>$v) {
                            if (!in_array($k, array('status', 'data'), true)) {
                                $outArr[$k] = $v;
                            }
                        }
                    }
                    $outArr['result'] = $data;
                } else {
                    $outArr = $msg;
                }
                $xml = new BaseModelXML();
                $xml->setSerializerOption(XML_SERIALIZER_OPTION_ENCODING, $oe);
                echo $xml->encode($outArr);
            break;
            case 'json':
                $outArr = array();
                if (!is_array($msg)) {
                    $outArr['status']['code'] = $code;
                    $outArr['status']['msg'] = $msg;
                    if (is_array($otherData)) {
                        foreach ($otherData as $k=>$v) {
                            if (!in_array($k, array('status', 'data'), true)) {
                                $outArr[$k] = $v;
                            }
                        }
                    }
                    $outArr['result'] = $data;
                } else {
                    $outArr = $msg;
                }
                $json = json_encode($outArr);
                $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
                if (preg_match("/^[a-zA-Z][a-zA-Z0-9_\.]+$/", $callback)) {
                    if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') { //POST
                        header("Content-Type: text/html");
                        $refer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : array();
                        if(!empty($refer) && (substr($refer['host'],-8,8)=='chope.co')){
                            $result = '<script>document.domain="chope.co";';
                        }else{
                            $result = '<script>document.domain="chope.net.cn";';
                        }
                        $result .= "parent.{$callback}({$json});</script>";
                        echo $result;
                    } else {
                        if(isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                            header('Content-Type: text/javascript; charset=UTF-8');
                        } else {
                            header('Content-Type: application/javascript; charset=UTF-8');
                        }
                        echo "{$callback}({$json});";
                    }
                } elseif ($callback) {
                    header('Content-Type: text/html; charset=UTF-8');
                    echo 'callback参数包含非法字符！';
                } else {
                    if(isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                        header('Content-Type: text/plain; charset=UTF-8');
                    } else {
                        header('Content-Type: application/json; charset=UTF-8');
                    }
                    echo $json;
                }
            break;
            default:
                try {
                    $tpl = new Base_View();
                    $tpl->assign('msg', $msg);
                    $tpl->assign('url', $url);
                    $tpl->assign('t', $t);
                    if ($code == '0') {
                        $tpl->display('message/message.html');
                    } else {
                        $tpl->display('message/error.html');
                    }
                } catch(Exception $e) {
                    
                    print($html);
                }
            break;
        }
        exit;
    }
}
