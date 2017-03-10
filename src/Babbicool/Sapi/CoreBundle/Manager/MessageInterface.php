<?php

namespace Babbicool\Sapi\CoreBundle\Manager;

/**
 *
 * @author babbicool
 */
interface MessageInterface {
    
    
    public function getMessage();
    public function getSessionId();
    public function setSessionId($sesionId);
    
    
}
