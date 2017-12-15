<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of webClient
 *
 * @author admin
 */

$url =  $_REQUEST['url'];
$path = $_REQUEST['path'];
$data = $_REQUEST['data'];

if (isset($url) && $url!="") {
    $httpType = "GET";
    if ($_REQUEST['type']) {
        $httpType = strtoupper($_REQUEST['type']);
    }
    echo json_encode(webClient::http_curl($url, $data, $httpType));
}elseif (isset($path) && $path!="") {
    $client = new webClient();
    echo json_encode($client->setFileContent($path, $data));
}

//echo json_encode(webClient::curl($url, $data));

class webClient {
    /**
     * 通过http_curl方式调用远程HTTP链接
     * @param type $url
     * @param type $data
     * @param type $httpType
     * @return type
     */
    public static function http_curl($url, $data = null, $httpType = 'GET', $timeout = 10, $linktimeout = 2) {
		if ($url == null) {
			return array("code"=>"-1", "msg"=>"no request");
		}
        $bs = microtime(true);
        $logs = '';
        $logsp = "[" . date('Y-m-d H:i:s') . "] ";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $linktimeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if ($httpType == 'POST') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            $postString = '';
            if (is_array($data) || is_object($data)) {
                foreach ($data as $key => $value) {
                    $postString .= $key . '=' . ($value) . '&';
                }
                $postString = substr($postString, 0, -1);
            }
            $logs .= $url . "?" . $postString . "\n";
    //        print_r($url.'?'.$postString);exit;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        } elseif ($httpType == 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            $getString = '';
            if (is_array($data) || is_object($data)) {
                foreach ($data as $key => $value) {
                    $value = ($value);
                    $getString .= $key . '=' . $value . '&';
                }
                $getString = substr($getString, 0, -1);
                $getString = '?' . $getString;
            }
            $logs .= $url . $getString . "\n";
            // print_r($data);
            // print_r($url . $getString.'</br>');exit();
            curl_setopt($ch, CURLOPT_URL, $url . $getString);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $logsp .= '[' . ( microtime(true) - $bs) . '] [' . $http_code . '] ';
        $logs = $logsp . "\r\n--REQUEST--" . $logs;
        $logs = $logs . "\r\n--RESPONSE--" . $response . "\r\n\r\n";
        //TYLog::interlog($logs);

		if ($response === false) {
			$logs .= "-- ERROR -- : " . curl_error($ch) . "\n";
		}

        //测试用
        $filepath = dirname(__FILE__) . '/logs/';
        if (!file_exists($filepath)) {
          mkdir($filepath);
        }
        file_put_contents($filepath.date("Ymd").'.log',$logs."\n\n\n",FILE_APPEND);
        if ($http_code == 200) {
            return $response;
        } else {
            return false;
        }
    }
	
	function curl($url, $data=NULL, $httpType='GET') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		if ($ssl) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		}
		if ($httpType == 'POST') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			$postString = '';
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					$postString .= $key . '=' . $value . '&';
				}
				$postString = substr($postString, 0, -1);
			} else {
				$postString = $data;
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		} elseif ($httpType == 'GET') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			$getString = '';
			if ($data) {
				foreach ($data as $key => $value) {
					$value = urlencode($value);
					$getString .= $key . '=' . $value . '&';
				}
				$getString = substr($getString, 0, -1);
				$getString = '?' . $getString;
			}
            curl_setopt($ch, CURLOPT_URL, $url . $getString);
		}
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($http_code == 200) {
			return $response;
		} else {
			return false;
		}
	}

    public function setFileContent($path,$content){
        $filepath = dirname(__FILE__).$path;
        if (!file_exists($filepath)) {
            $re = mkdir(dirname($filepath),0777,true);
        }
        $res = file_put_contents($filepath,$content);
        $ret['code'] = '0';
        $ret['msg'] = "";
        $ret['info'] = "nothing";

        return $ret;
    }
}
?>
