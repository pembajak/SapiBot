<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Babbicool\Sapi\SkoutBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Babbicool\Sapi\SkoutBundle\Manager\SkoutApiManager;


/**
 * Description of ScamBomCommand
 *
 * @author babbicool
 */
class ScamBomCommand extends ContainerAwareCommand {
    //put your code here
    
    const USER_DB = "user.json";
    const SCAM = "scam.json";
    private $skoutApiManager;
    
    protected function configure() {
        $this->setName('SkoutApi:scamboom')
                ->setDescription('Skout script auto comment & chat');
                //->addArgument('uid', InputArgument::REQUIRED, 'skout user id');
    }
    
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function randomEmail(){
      return generateRandomString()."@gmail.com";
    }
    
    
    
    protected function execute(InputInterface $input, OutputInterface $output) {
            
        $target = "";
        $this->skoutApiManager = new SkoutApiManager();
        if($this->register()){
            
            while (true) {
                $int =  1023430;
                $userBuzz = $this->skoutApiManager->getUserBuzz($target);
                if(!$userBuzz)
                    $this->register();
                
                foreach ($userBuzz['elements'] as $key => $value){
                    for ($index = 0; $index < 5; $index++) {                        
                        $id = $value['buzz']['id'];
                        var_dump($id);
                        $this->skoutApiManager->putComment($id, "HATI HATI SCAM !!!! TERHADAP AKUN INI !!!!! ");
                        $statusSend = $this->skoutApiManager->putChat($target,"TEPU TEPU LU YAH ");
                        var_dump($statusSend);
                        $int++;
                    }
                }
               
            }
        }
    }
    
    
    public function register(){
        
        $user = json_decode(file_get_contents(self::USER_DB), true);
        if(!$user)
            $user = [];
        
        $email = $this->randomEmail();
        var_dump($email);
        $reg1 = $this->skoutApiManager->register($email, "passwordnya", "Pak Satpam","02/01/1970","male","Skout Scam Defender");
        if($reg1){
            $user = array_push($user, $email);
            file_put_contents(self::USER_DB,json_encode($user));
            return true;
        }
        return false;
    }
    
}
