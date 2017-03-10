<?php

namespace Babbicool\Sapi\CoreBundle\Manager;

use \Curl\Curl;
/**
 * Description of BotpressApiManager
 * nlp using Botpress rivescript
 * @author babbicool
 */
class BotpressApiManager implements MessageInterface{

    private $cookie_file = 'cookies_botpress.txt';

    private $sessionId = null;
    private $message = null;
    private $curl = null;
    private $url = "http://localhost:3000/api/botpress-rivescript/sendmessage";


    public function __construct($url,$sesionid = "default-ses") {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_USERAGENT, 'Dalvik/2.1.0 (Linux; U; Android 5.0.2; Mi 4i MIUI/6.12.22)');
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->sessionId = $sesionid;
        $this->url = $url;
    }
    public function getMessage() {
        return $this->message;
    }

    public function getSessionId() {
        return $this->sessionId;
    }
    public function setSessionId($sesionId) {
        $this->sessionId = $sesionId;
    }
    public function call($text){
        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->post($this->url, ["session"=>$this->sessionId,"text"=>$text]);
        $res =  json_decode($this->curl->rawResponse, true);
        $this->sessionId = $res["sessionid"];
        $data["action"] = ""; // not implement yet
        $data["score"] = 0.7; // not implement yet
        $data["response"] = $res["message"];
        return $data;
    }

}
