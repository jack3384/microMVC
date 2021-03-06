<?php
/**
 * modify by php-mod/curl https://github.com/php-mod/curl
 * MIT license
 *
 * Doc below
 * Quick Start and Examples

$curl = new Curl\Curl();
$curl->get('http://www.example.com/');
$curl = new Curl\Curl();
$curl->get('http://www.example.com/search', array(
'q' => 'keyword',
));
$curl = new Curl\Curl();
$curl->post('http://www.example.com/login/', array(
'username' => 'myusername',
'password' => 'mypassword',
));
$curl = new Curl\Curl();
$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('');
$curl->setReferrer('');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setCookie('key', 'value');
$curl->get('http://www.example.com/');

if ($curl->error) {
echo $curl->error_code;
}
else {
echo $curl->response;
}

var_dump($curl->request_headers);
var_dump($curl->response_headers);
$curl = new Curl\Curl();
$curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
$curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);
$curl->get('https://encrypted.example.com/');
$curl = new Curl\Curl();
$curl->put('http://api.example.com/user/', array(
'first_name' => 'Zach',
'last_name' => 'Borboa',
));
$curl = new Curl\Curl();
$curl->patch('http://api.example.com/profile/', array(
'image' => '@path/to/file.jpg',
));
$curl = new Curl\Curl();
$curl->delete('http://api.example.com/user/', array(
'id' => '1234',
));
$curl->close();
// Example access to curl object.
curl_set_opt($curl->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
curl_close($curl->curl);
 */

namespace jikai\Components;

class Curl
{
    // The HTTP authentication method(s) to use.
    const AUTH_BASIC = CURLAUTH_BASIC;
    const AUTH_DIGEST = CURLAUTH_DIGEST;
    const AUTH_GSSNEGOTIATE = CURLAUTH_GSSNEGOTIATE;
    const AUTH_NTLM = CURLAUTH_NTLM;
    const AUTH_ANY = CURLAUTH_ANY;
    const AUTH_ANYSAFE = CURLAUTH_ANYSAFE;
    const USER_AGENT = '';
    private $_cookies = array();
    private $_headers = array();
    public $curl;
    public $error = false;
    public $error_code = 0;
    public $error_message = null;
    public $curl_error = false;
    public $curl_error_code = 0;
    public $curl_error_message = null;
    public $http_error = false;
    public $http_status_code = 0;
    public $http_error_message = null;
    public $request_headers = null;
    public $response_headers = null;
    public $response = null;
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }
        $this->init();
    }

    protected function getResponse()
    {
        if ($this->error) {
            //throw new \Exception("curl is not success<br>errCode:".$this->error_code.". msg:".$this->error_message);
            return false;
        }
        else {
            return $this->response;
        }

    }

    public function get($url, $queryString = array())
    {
        if (count($queryString) > 0) {
            $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($queryString));
        } else {
            $this->setopt(CURLOPT_URL, $url);
        }
        $this->setopt(CURLOPT_HTTPGET, true);
        $this->_exec();
        //修改添加直接获得返回值
       return $this->getResponse();
    }
    public function post($url, $data = array(),$queryString=array())
    {
        if (count($queryString) > 0) {
            $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($queryString));
        } else {
            $this->setopt(CURLOPT_URL, $url);
        }
        $this->setopt(CURLOPT_POST, true);
        if (is_array($data) || is_object($data))
        {
            $data = http_build_query($data);
        }
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();
        //修改添加直接获得返回值
        return $this->getResponse();
    }
    public function put($url, $data = array(), $json = 0)
    {
        if ($json == 0) {
            $url .= '?' . http_build_query($data);
        } else {
            $this->setopt(CURLOPT_POST, true);
            if (is_array($data) || is_object($data)) {
                $data = http_build_query($data);
            }
            $this->setopt(CURLOPT_POSTFIELDS, $data);
        }
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->_exec();

        //修改添加直接获得返回值
        return $this->getResponse();
    }
    public function patch($url, $data = array())
    {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();

        //修改添加直接获得返回值
        return $this->getResponse();
    }
    public function delete($url, $data = array())
    {
        $this->setopt(CURLOPT_URL, $url . '?' . http_build_query($data));
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->_exec();

        //修改添加直接获得返回值
        return $this->getResponse();
    }
    public function setBasicAuthentication($username, $password)
    {
        $this->setHttpAuth(self::AUTH_BASIC);
        $this->setopt(CURLOPT_USERPWD, $username . ':' . $password);
    }
    protected function setHttpAuth($httpauth)
    {
        $this->setOpt(CURLOPT_HTTPAUTH, $httpauth);
    }
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $key . ': ' . $value;
        $this->setopt(CURLOPT_HTTPHEADER, array_values($this->_headers));
    }
    public function setUserAgent($user_agent)
    {
        $this->setopt(CURLOPT_USERAGENT, $user_agent);
    }
    public function setReferrer($referrer)
    {
        $this->setopt(CURLOPT_REFERER, $referrer);
    }
    public function setCookie($key, $value)
    {
        $this->_cookies[$key] = $value;
        $this->setopt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }
    public function setOpt($option, $value)
    {
        return curl_setopt($this->curl, $option, $value);
    }
    public function verbose($on = true)
    {
        $this->setopt(CURLOPT_VERBOSE, $on);
    }
    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }
    public function reset()
    {
        $this->close();
        $this->_cookies = array();
        $this->_headers = array();
        $this->error = false;
        $this->error_code = 0;
        $this->error_message = null;
        $this->curl_error = false;
        $this->curl_error_code = 0;
        $this->curl_error_message = null;
        $this->http_error = false;
        $this->http_status_code = 0;
        $this->http_error_message = null;
        $this->request_headers = null;
        $this->response_headers = null;
        $this->response = null;
        $this->init();
    }
    public function _exec()
    {
        $this->response = curl_exec($this->curl);
        $this->curl_error_code = curl_errno($this->curl);
        $this->curl_error_message = curl_error($this->curl);
        $this->curl_error = !($this->curl_error_code === 0);
        $this->http_status_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->http_error = in_array(floor($this->http_status_code / 100), array(4, 5));
        $this->error = $this->curl_error || $this->http_error;
        $this->error_code = $this->error ? ($this->curl_error ? $this->curl_error_code : $this->http_status_code) : 0;
        $this->request_headers = preg_split('/\r\n/', curl_getinfo($this->curl, CURLINFO_HEADER_OUT), null, PREG_SPLIT_NO_EMPTY);
        $this->response_headers = '';
        if (!(strpos($this->response, "\r\n\r\n") === false)) {
            list($response_header, $this->response) = explode("\r\n\r\n", $this->response, 2);
            while (strtolower(trim($response_header)) === 'http/1.1 100 continue') {
                list($response_header, $this->response) = explode("\r\n\r\n", $this->response, 2);
            }
            $this->response_headers = preg_split('/\r\n/', $response_header, null, PREG_SPLIT_NO_EMPTY);
        }
        $this->http_error_message = $this->error ? (isset($this->response_headers['0']) ? $this->response_headers['0'] : '') : '';
        $this->error_message = $this->curl_error ? $this->curl_error_message : $this->http_error_message;
        return $this->error_code;
    }
    public function __destruct()
    {
        $this->close();
    }
    private function init()
    {
        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setopt(CURLINFO_HEADER_OUT, true);
        $this->setopt(CURLOPT_HEADER, true);
        $this->setopt(CURLOPT_RETURNTRANSFER, true);
        //关闭ssl身份验证提高兼容性
        $this->setopt(CURLOPT_SSL_VERIFYPEER, false);
    }
}