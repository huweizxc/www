<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";
include "upyun.class.php";
include "upyun.config.php";

define('DS', DIRECTORY_SEPARATOR);


/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}

/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);

$info = $up->getFileInfo();

// 将文件同步存储到 UPYUN
/*
function log($log) {
    $log_file = "log.txt";

    $handle = fopen($log_file, "a");
    fwrite($handle, $log);
    fclose($handle);
    exit;
}
 */

$upyun = new UpYun($bucketname, $username, $password);

try {
    $uri = strstr($info["url"], "Uploads");
    $u = realpath('..').DS.'..'.DS.'..'.DS.$uri;
    
    if (file_exists($u)) {
         file_put_contents("1.txt", var_export($uri, true)."\r\n",FILE_APPEND); 
        $opts = array(
            UpYun::CONTENT_MD5 => md5(file_get_contents($u))
        );

        $fh = fopen($u, "rb");
        $rsp = $upyun->writeFile("/".$uri, $fh, True, $opts);
        fclose($fh);
    }
    else {
        $log = date("Y-m-d H:m:s") . " 文件不存在，请检查目录是否正确。" . "\r\n";
        $log_file = "log.txt";

        $handle = fopen($log_file, "a");
        fwrite($handle, $log);
        fclose($handle);
        exit;
    }
}
catch(Exception $e) {
    $log = date("Y-m-d H:m:s") . " " . $e->getCode() . " " . $e->getMessage() . "\r\n";
    $log_file = "log.txt";

    $handle = fopen($log_file, "a");
    fwrite($handle, $log);
    fclose($handle);
    exit;
}

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
return json_encode($up->getFileInfo());
