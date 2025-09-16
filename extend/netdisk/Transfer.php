<?php

namespace netdisk;

class Transfer
{
    public function __construct()
    {
        // 原 Open 类的构造函数内容
        $this->url = ""; // 资源地址
        $this->expired_type = 1; //有效期 1分享永久 2临时
        $this->ad_fid = ""; //夸克专用 - 分享时带上这个文件的fid
        $this->code = ""; //分享码
        $this->isType = 0; //0 转存并分享后的资源信息  1直接获取资源信息 
    }

    public function getFiles($type = 0, $pdir_fid = 0)
    {
        if ($type == 1) {
            //阿里
            $pan = new \netdisk\pan\AlipanPan();
            return $pan->getFiles($pdir_fid);
        } else if ($type == 2) {
            //百度网盘
            $pan = new \netdisk\pan\BaiduPan();
            return $pan->getFiles($pdir_fid);
        } else if ($type == 3) {
            //UC
            $pan = new \netdisk\pan\UcPan();
            return $pan->getFiles($pdir_fid);
        } else if ($type == 4) {
            //迅雷
            $pan = new \netdisk\pan\XunleiPan();
            return $pan->getFiles($pdir_fid);
        } else {
            //夸克
            $pan = new \netdisk\pan\QuarkPan();
            return $pan->getFiles($pdir_fid);
        }
    }

    public function transfer($urlData = [])
    {
        $url = $urlData['url'] ?? '';
        $config = [
            'isType' => $urlData['isType'] ?? input('isType') ?? 0,
            'url' => $url,
            'code' => $urlData['code'] ?? input('code') ?? '',
            'expired_type' => $urlData['expired_type'] ?? input('expired_type') ?? 1,
            'ad_fid' => $urlData['ad_fid'] ?? input('ad_fid') ?? "",
            'stoken' => $urlData['stoken'] ?? '',
        ];

        // 如果URL中包含提取码，自动解析提取码
        if (strpos($url, '?pwd=') !== false) {
            $parsedUrl = parse_url($url);
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $query);
                if (isset($query['pwd']) && !empty($query['pwd'])) {
                    $config['code'] = $query['pwd'];
                    // 移除URL中的提取码部分
                    $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
                    if (isset($parsedUrl['fragment'])) {
                        $url .= '#' . $parsedUrl['fragment'];
                    }
                }
            }
        }

        if (strpos($url, '?entry=') !== false) {
            $entry = preg_match('/\?entry=([^&]+)/', $url, $matches) ? $matches[1] : '';
            $url = preg_match('/.*(?=\?entry=)/', $url, $matches) ? $matches[0] : '';
        }

        $substring = strstr($url, 's/');
        if ($substring !== false) {
            $pwd_id = substr($substring, 2); // 去除 's/' 部分
            // 移除问号及后面的参数
            $questionMarkPos = strpos($pwd_id, '?');
            if ($questionMarkPos !== false) {
                $pwd_id = substr($pwd_id, 0, $questionMarkPos);
            }
        } else {
            return jerr2("资源地址格式有误");
        }

        $patterns = [
            'pan.quark.cn' => 0,
            'www.alipan.com' => 1,
            'www.aliyundrive.com' => 1,
            'pan.baidu.com' => 2,
            'drive.uc.cn' => 3,
            'fast.uc.cn' => 3,
            'pan.xunlei.com' => 4,
        ];

        $url_type = -1;  // 默认值为 -1
        foreach ($patterns as $pattern => $type) {
            if (strpos($url, $pattern) !== false) {
                $url_type = $type;
                break;  // 一旦匹配成功，退出循环
            }
        }

        $this->url = $url;

        if ($url_type == 0) {
            //夸克
            $pan = new \netdisk\pan\QuarkPan($config);
            return $pan->transfer(strtok($pwd_id, '#'));
        } else if ($url_type == 1) {
            //阿里
            $pan = new \netdisk\pan\AlipanPan($config);
            return $pan->transfer(strtok($pwd_id, '#'));
        } else if ($url_type == 2) {
            //百度网盘
            $pan = new \netdisk\pan\BaiduPan($config);
            return $pan->transfer(strtok($pwd_id, '#'));
        } else if ($url_type == 3) {
            //UC
            $pan = new \netdisk\pan\UcPan($config);
            return $pan->transfer(strtok($pwd_id, '#'));
        } else if ($url_type == 4) {
            //迅雷
            $pan = new \netdisk\pan\XunleiPan($config);
            return $pan->transfer(strtok($pwd_id, '#'));
        } else {
            return jerr2("资源地址格式有误");
        }
    }


    public function deletepdirFid($type = 0, $filelist)
    {
        if ($type == 1) {
            //阿里
            $pan = new \netdisk\pan\AlipanPan();
            return $pan->deletepdirFid($filelist);
        } else if ($type == 2) {
            //百度网盘
            $pan = new \netdisk\pan\BaiduPan();
            return $pan->deletepdirFid($filelist);
        } else if ($type == 3) {
            //UC
            $pan = new \netdisk\pan\UcPan();
            return $pan->deletepdirFid($filelist);
        } else if ($type == 4) {
            //迅雷
            $pan = new \netdisk\pan\XunleiPan();
            return $pan->deletepdirFid($filelist);
        } else {
            //夸克
            $pan = new \netdisk\pan\QuarkPan();
            return $pan->deletepdirFid($filelist);
        }
    }
}
