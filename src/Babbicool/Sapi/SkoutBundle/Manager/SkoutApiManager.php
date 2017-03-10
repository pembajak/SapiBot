<?php

namespace Babbicool\Sapi\SkoutBundle\Manager;

use \Curl\Curl;
use Babbicool\Sapi\CoreBundle\Utils\SimpleHtmlDom;
use Babbicool\Sapi\CoreBundle\Utils\String;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version0X;

/**
 * Description of SkoutApiManager
 * CONTACT ME TO GET SIGNATURE KEYS [pembajakanya@gmail.com]
 * @author babbicool
 */
class SkoutApiManager {

    const HOST = "https://and.flurv.com/";
    const applicationKeys = "78231c9fd1e4ec7a850022b84fd700f4";

    private $cookie_file = 'cookies.txt';
    private $googleApiKey = "6Lc2qN8SAAAAAEe8_R2ALF4hu1V_x34nUV1mzW-W";
    private $clientWss = null;

    public $curl = null;
    public $SESSION_ID = "";

    public function __construct() {
        $this->cookie_file = String::gen_uuid() . "_" . $this->cookie_file;
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_USERAGENT, 'Dalvik/2.1.0 (Linux; U; Android 5.0.2; Mi 4i MIUI/6.12.22)');
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
    }

    public function getProfile() {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/me', []);
        $rest = json_decode($this->curl->rawResponse, true);
        return $rest;
    }

    public function updateLocation($cityName, $stateName, $countryName, $lat, $lon) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->post(self::HOST . 'api/1/me/location', array("application_code" => self::applicationKeys,
            "city_name" => $cityName,
            "state_name" => $stateName,
            "country_name" => $countryName,
            "latitude" => $lat,
            "longitude" => $lon,
            "provider" => "Google",
            "location_locale" => "en",
            "rand_token" => String::gen_uuid(),)
        );

        $rest = json_decode($this->curl->rawResponse, true);

        return $rest;
    }

    public function getUserBuzz($userid, $count = 100, $commnent = 0) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/users/' . $userid . '/buzzes', array("count" => $count, "num_comments" => $commnent, "since_id" => "0"));
        $rest = json_decode($this->curl->rawResponse, true);

        return $rest;
    }

    public function getDetailBuzz($id, $commnent = 0) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/buzzes/' . $id, array("num_comments" => $commnent));

        $rest = json_decode($this->curl->rawResponse, true);
        return $rest;
    }

    public function getComment($id, $count = 100) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/buzzes/' . $id . '/comments', array("count" => $count, "most_recent" => "true", "since_id" => "158808103"));

        $rest = json_decode($this->curl->rawResponse, true);
        return $rest;
    }

    public function getLocalBuzz($count = 60, $num_comments = 1) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/buzzes/local', ["count" => $count, "num_comments" => $num_comments,"since_id"=>0]);

        $rest = json_decode($this->curl->rawResponse);

        return $rest;
    }

    public function getPeople($limit = 100, $offset = 0, $other_max_age = 50, $other_min_age = 9) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/users/search', array(
            "include_buzz" => "false",
            "limit" => $limit,
            "offset" => $offset,
            "other_ethnicity_multi" => "ask_me",
            "other_gender" => "both",
            "other_interested_in" => "both",
            "other_max_age" => $other_max_age,
            "other_min_age" => $other_min_age,
            "search_level" => "9"
        ));

        $rest = json_decode($this->curl->rawResponse, true);

        return $rest;
    }

    public function getUserDetail($userid) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/users/' . $userid, array("whoCheckedMeOut" => "true"));

        $rest = json_decode($this->curl->rawResponse);
        return $rest;
    }

    public function putComment($id, $comments) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->post('https://www.skout.com/api/1/buzzes/' . $id . '/comments/add', array(
            "comment" => "" . $comments
        ));
        $rest = json_decode($this->curl->rawResponse, true);

        return $rest;
    }

    public function putChat($idUser, $msg) {
        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->post(self::HOST . "api/1/chats/$idUser/send", array("message" => $msg));
        $rest = json_decode($this->curl->rawResponse, true);
        return $rest;
    }

    public function register($email, $password, $name, $birthday_date = "02/01/1992", $gender = "male", $city_name = "Numpang Lewat", $state_name = "West Java", $country_name = "Indonesia", $latitude = "-6.916782", $longitude = "107.618388", $session_id = "_", $macAddress = "DC-E6-88-24-71-04", $deviceid = "9774d56d682e549c") {

        $signature = "CONTACT ME TO GET SIGNATURE KEYS [pembajakanya@gmail.com]" . "53" . $email . $password . "2" . $birthday_date . "Android FLURV 4.23.1" . "0" . "0" . "en_US" . $macAddress . $deviceid;
        $key = hash('sha256', $signature);

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $session_id",
            "signature: $key",
            "app_name: flurv",
            "version: 53",
            "app_version: 4.23.1",
            "app_device: android",
            "Accept-Encoding: gzip",
            "Connection: Keep-Alive",
            "charset: utf-8"
                )
        );

        $this->curl->setOpt(CURLOPT_USERAGENT, 'Dalvik/2.1.0 (Linux; U; Android 5.0.2; Mi 4i MIUI/6.12.22)');
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->post("https://and.flurv.com/api/1/auth/register/mobile", array("application_code" => "78231c9fd1e4ec7a850022b84fd700f4",
            "param0" => $email,
            "param1" => $password,
            "name" => $name,
            "gender" => $gender,
            "birthday_date" => $birthday_date,
            "interested_in" => "both",
            "ethnicity" => "ask_me",
            "search_level" => "1",
            "search_min_age" => "0",
            "search_max_age" => "0",
            "locale" => "en_US",
            "device_brand" => "",
            "device_model" => "",
            "device_id" => $deviceid,
            "device_mac" => $macAddress,
            "device_open_id" => $deviceid,
            "os_version" => "21",
            "ui" => "Android FLURV 4.23.1",
            "latitude" => $latitude,
            "longitude" => $longitude,
            "city_name" => $city_name,
            "state_name" => $state_name,
            "country_name" => $country_name,
            "location_locale" => "en"), true);

        $rest = json_decode($this->curl->rawResponse, true);

        if ($rest['status_code'] == '53') {
            $this->SESSION_ID = $rest['session_id'];
            $rest = $this->getBarrier();
            return $rest;
        } else {
            $this->SESSION_ID = $rest['session_id'];
            return $rest;
        }
        return $rest;
    }

    public function getBarrier() {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID "));
        $this->curl->setOpt(CURLOPT_USERAGENT, 'Dalvik/2.1.0 (Linux; U; Android 5.0.2; Mi 4i MIUI/6.12.22)');
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->get(self::HOST . 'api/1/barriers', [], true);
        $rest = json_decode($this->curl->rawResponse, true);
        var_dump($rest);

        $this->googleApiKey = $rest["user_data"]["re_captcha_public_key"];


        $returnCapca = $this->getAudioFile();
        $this->SESSION_ID = $returnCapca;
        var_dump($returnCapca);
        return true;
    }

    public function setFav($userId) {

        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID",
            "app_name: flurv",
            "app_version: 4.23.1",
            "app_device: android",
            "Accept-Encoding: gzip",
            "Connection: Keep-Alive",
            "charset: utf-8"
                )
        );

        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->post(self::HOST . 'api/1/me/favorites/add', ["application_code" => self::applicationKeys, "user_id" => $userId, "rand_token" => String::gen_uuid()], true);

        return json_decode($this->curl->rawResponse, true);
    }

    /* ***********
     *  socket.io
     * ***********/

    public function initWss() {

        $this->clientWss = new Client(new Version0X('https://www.skout.com/socket.io/'));
        $this->clientWss->initialize();
        $this->clientWss->emit('login', ['sessionId' => $this->SESSION_ID]);
    }

    public function listenWss() {
        $r = $this->clientWss->keepAlive();
    }

    public function readWss() {
        try {
            $r = $this->clientWss->read();
            if (!empty($r)) {
                $jsonData = array();
                $data = explode(":::", $r);
                if ($data[0] == 5) {
                    $jsonData = json_decode($data[1], true);
                    if ($jsonData['name'] == 're-login')
                        $this->clientWss->emit('login', ['sessionId' => $this->SESSION_ID]);

                    return $jsonData;
                }else {
                    $this->clientWss->ping();
                }
                return false;
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return false;
    }

    public function closeWss() {
        $this->clientWss->close();
    }

    /****************
     * end socket.io
     ****************/

    public function getNotification() {
        $this->curl->setOpt(CURLOPT_HTTPHEADER, array("session_id: $this->SESSION_ID",
            "app_name: flurv",
            "app_version: 4.23.1",
            "app_device: android",
            "Accept-Encoding: gzip",
            "Connection: Keep-Alive",
            "charset: utf-8"
                )
        );

        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->get(self::HOST . 'api/1/me/notifications', ["start" => "0", "limit" => "1", "application_code" => self::applicationKeys, "rand_token" => String::gen_uuid()], true);
        if ($this->curl->rawResponse) {
            return json_decode($this->curl->rawResponse, true);
        }
        return false;
    }

    /* recapcha solver */

    public function getAudioFile() {

        /** getting Cookie * */
        $this->curl->setOpt(CURLOPT_TIMEOUT, 45);
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->curl->get("http://a.flurv.com/support/captchaMobile/", ["sid" => $this->SESSION_ID]);

        $this->curl->setOpt(CURLOPT_TIMEOUT, 45);
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->curl->get("https://www.google.com/recaptcha/api/noscript?k=" . $this->googleApiKey . "&is_audio=true", []);

        $url = "";
        $html = SimpleHtmlDom::str_get_html($this->curl->rawResponse);
        foreach ($html->find('a') as $link) {
            if (strpos($link->href, "image?c=") > -1)
                $url = $link->href;
        }

        $this->recaptcha_challenge_field = str_replace("image?c=", "", $url);

        @unlink("sound.mp3");
        @unlink("output.flac");

        $captchastring = file_get_contents("http://www.google.com/recaptcha/api/" . $url);
        file_put_contents("sound.mp3", $captchastring);

        // convert to flac
        $handle = popen('ffmpeg -y -i sound.mp3 -ar 16000 -ab 48k output.flac 2>&1', 'r');
        if ($handle !== false) {
            while (($char = fgetc($handle)) !== false) {
                // working
            }
            $returnVar = pclose($handle);
        }
        if ($returnVar === 0) {
            // converted successfully
        } else {
            exit('Error#Converting');
        }

        $getFlac = file_get_contents('output.flac');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/speech-api/v2/recognize?client=chromium&lang=en_US&key=AIzaSyAcalCzUvPmmJ7CZBFOEWx2Z1ZSn4Vs1gg');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '@' . $getFlac);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: audio/x-flac; rate=16000'));

        $googleVoice = curl_exec($ch);

        curl_close($ch);
        $resjson = json_decode(str_replace('{"result":[]}', "", "$googleVoice"), true);
        $resjson = $resjson["result"][0]['alternative'];

        $submit = $this->submitRecapcha($resjson[0]['transcript']);
        return $submit;
    }

    public function submitRecapcha($text) {

        $this->curl->setOpt(CURLOPT_TIMEOUT, 45);
        $this->curl->setOpt(CURLOPT_COOKIEJAR, realpath($this->cookie_file));
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);

        $this->curl->get("http://a.flurv.com/support/captchaMobile/", ["recaptcha_challenge_field" => $this->recaptcha_challenge_field, "recaptcha_response_field" => $text]);

        //var_dump($this->curl->rawResponse );
        if (strpos($this->curl->rawResponse, "<ax21:statusCode>0</ax21:statusCode>") > -1) {
            var_dump($this->curl->rawResponse);
            $strBracked = "<ax21:sessionId>";
            $s = strpos($this->curl->rawResponse, $strBracked);
            return str_replace($strBracked, "", substr($this->curl->rawResponse, $s, 36 + strlen($strBracked)));
        }
        return $this->getAudioFile();
    }

}
