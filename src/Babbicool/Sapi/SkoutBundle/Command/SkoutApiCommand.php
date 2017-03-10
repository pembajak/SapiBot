<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Babbicool\Sapi\SkoutBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Babbicool\Sapi\SkoutBundle\Manager\SkoutApiManager;
use Babbicool\Sapi\CoreBundle\Utils\String;
use ApiAi\Client;
use ApiAi\Model\Query;
use ApiAi\Method\QueryApi;
use Babbicool\Sapi\CoreBundle\Manager\BotpressApiManager;

/**
 * Description of SkoutApiCommand
 *
 * @author babbicool
 */
class SkoutApiCommand extends ContainerAwareCommand {

    const NOTIFICATION_CHECKED = 53;
    const NOTIFICATION_USER_BUZZ = 67;
    const NOTIFICATION_USER_BUZZ_PICTURE = 69;
    const NOTIFICATION_FAV = 54;
    const NOTIFICATION_COMMENT = 65;
    const NOTIFICATION_LIKE = 66;
    const CHAT_ACTION_MSG = 0;
    const CHAT_ACTION_BUZZ = 1;
    const APIAI_TOKEN = 'a4c53e9c314f4f7ebc4d6735c2cb8e99';
    const BOT_MASTER = "114191197";
    const MENTION_ME = "sapi";

    private $skoutApiManager;
    private $selfId = "116847552";

    protected function configure() {
        $this->setName('SkoutApi:test')
                ->setDescription('Api Tester');
    }

    public function parse($notif) {
        switch ($notif['name']) {
            case "notification":
                switch ($notif['args'][0]["type"]) {
                    case self::NOTIFICATION_USER_BUZZ:
                        $this->actionBuzz($notif['args'][0]["fromUserId"], $notif['args'][0]["data"]);
                        break;
                     case self::NOTIFICATION_USER_BUZZ_PICTURE:
                        $this->actionBuzz($notif['args'][0]["fromUserId"], $notif['args'][0]["data"]);
                        break;
                    case self::NOTIFICATION_FAV:
                        $this->actionFav($notif['args'][0]["fromUserId"]);
                        break;
                    case self::NOTIFICATION_COMMENT:
                        if ($notif['args'][0]["fromUserId"] != $this->selfId) {
                            $this->actionComment($notif['args'][0]["fromUserId"], $notif['args'][0]["data"]);
                        }
                        break;
                    case self::NOTIFICATION_CHECKED:
                        $this->actionChecked($notif['args'][0]["fromUserId"]);
                        break;
                    default:
                        break;
                }
                break;
            case "chat":
                if ($notif['args'][0]["from"] != $notif['args'][0]["to"] && $notif['args'][0]["from"] != $this->selfId) {
                    $this->actionChat($notif['args'][0]["from"], $notif['args'][0]["text"]);
                }
                break;
            default:
                break;
        }
    }

    public function actionChecked($fromUserId) {
        $nlpIntert = $this->apiAiExtraction("342213123144566", $fromUserId);
        $this->skoutApiManager->putChat($fromUserId, $nlpIntert['response']);
    }

    public function actionComment($fromUserId, $data) {
        if ($fromUserId == $this->selfId)
            return;

        $buzz = $this->skoutApiManager->getDetailBuzz($data, 1);
        $comments = $buzz['comments']['elements'];
        foreach ($comments as $key => $comment) {
            if ($comment['creator']['id'] != $this->selfId) {
                $pos = explode(self::MENTION_ME, strtolower($comment['message']));
                var_dump($comment['message']);
                var_dump($pos);
                if (count($pos) > 1) {
                    $nlpIntert = $this->apiAiExtraction($comment['message'], $fromUserId);
                    switch ($nlpIntert['action']) {
//                        case 'spamcom':
//                            $this->skoutApiManager->putComment($data, $nlpIntert['response']);
//                            if ($fromUserId == self::BOT_MASTER)
//                                $this->subActionSpam();
//                            break;
                        default:
                            $this->skoutApiManager->putComment($data, $nlpIntert['response']);
                            break;
                    }
                }
            }
        }
    }

    public function actionBuzz($from, $data) {
        $userBuzz = $this->skoutApiManager->getDetailBuzz($data);
        $text = $userBuzz['buzz']['caption'];
        $nlpIntert = $this->apiAiExtraction($text, $from);
        switch ($nlpIntert['action']) {
//            case 'spamcom':
//                 $this->skoutApiManager->putComment($data,$nlpIntert['response']);
//                 if($from== self::BOT_MASTER)
//                     $this->subActionSpam();
//            break;
            default:
                $this->skoutApiManager->putComment($data, $nlpIntert['response']);
                break;
        }
    }

    public function actionChat($from, $text, $type = self::CHAT_ACTION_MSG) {
        $nlpIntert = $this->apiAiExtraction($text, $from);
        switch ($nlpIntert['action']) {
//            case 'spamcom':
//                 $this->skoutApiManager->putChat($from, $nlpIntert['response']);
//                 if($from== self::BOT_MASTER)
//                     $this->subActionSpam();
//            break;
            default:
                $this->skoutApiManager->putChat($from, $nlpIntert['response']);
                break;
        }
    }

    public function subActionSpam() {

        echo "Starting Spam local" . PHP_EOL;


        //$nlpIntert = $this->apiAiExtraction("random");
        $buzzs = $this->skoutApiManager->getLocalBuzz();
        var_dump($buzzs);
        foreach ($buzzs->elements as $buzz) {
          //  $this->skoutApiManager->putComment($buzz->buzz->id, $nlpIntert['response']);
           $var =  $this->skoutApiManager->putComment($buzz->buzz->id, "Wa ndang ultah , uhuuuuy");
           var_dump($var);

        }
    }

    public function apiAiExtraction($text, $from = "userid") {
        try {

            /* using bot press */

            $client = new BotpressApiManager("http://localhost:3000/api/botpress-rivescript/sendmessage",$from);

            return $client->call($text);

//            $client = new Client(self::APIAI_TOKEN);
//            $queryApi = new QueryApi($client);
//            $text = str_replace(self::MENTION_ME, "", $text);
//
//
//            $meaning = $queryApi->extractMeaning($text, [
//                'sessionId' => $from,
//                'lang' => 'id',
//            ]);
//            $response = new Query($meaning);
//            $ret['action'] = $response->getResult()->getAction();
//            $ret['score'] = $response->getResult()->getScore();
//            $ret['response'] = $response->getResult()->getFulfillment()->getSpeech();
//
//            var_dump($ret);
//
//            return $ret;





        } catch (\Exception $error) {
            echo $error->getMessage();
        }
    }

    public function actionFav($userid, $data = "") {
        $nlpIntert = $this->apiAiExtraction("342213123144567", $userid);
        $this->skoutApiManager->putChat($userid, $nlpIntert['response']);
        $this->skoutApiManager->setFav($userid);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->skoutApiManager = new SkoutApiManager();
        $reg1 = $this->skoutApiManager->register("blablablablablablablablabla1@gmail.com", "PASSWORDNYA", "Sapi");
        if ($reg1) {
            $this->skoutApiManager->updateLocation("Kandang Sapi", "Jawa Barat", "Indonesia", "-6.907134", "107.558803");
            $this->skoutApiManager->updateLocation("Kandang Sapi", "Jawa Barat", "Indonesia", "-6.907134", "107.558803");
            //$this->subActionSpam();

//            $this->skoutApiManager->updateLocation("Numpang Lewat", "Jawa Barat", "Indonesia", "35.674935", "139.681541");
//            $this->skoutApiManager->updateLocation("Numpang Lewat", "Jawa Barat", "Indonesia", "35.674935", "139.681541");

            $me = $this->skoutApiManager->getProfile();
            $this->selfId = $me['id'];
            $this->skoutApiManager->initWss();
            try {
                while (true) {
                    $notification = $this->skoutApiManager->readWss();
                    if ($notification) {
                        var_dump($notification);
                        $this->parse($notification);
                    }
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
            $this->skoutApiManager->closeWss();
        }
    }
}
