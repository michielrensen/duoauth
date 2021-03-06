<?php

namespace DuoAuth;

class Service extends \DuoAuth\Model
{
    /**
     * Ping the service to see if it's responding correctly
     * 
     * @param  boolean $returnTime Option to return time from ping (not true) on success
     * @return boolean|integer True/false on success/fail, int if return time requested
     */
    public function ping($returnTime = false)
    {
        $request = $this->getRequest('auth')
            ->setPath('/auth/v2/ping');

        $response = $request->send();

        if ($response->success()) {
            return ($returnTime === true) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Checks to ensure that the keys are correct and request is valid
     *     Useful for validating secret/integration keys
     * 
     * @return boolean Success/fail of request
     */
    public function check()
    {
        $request = $this->getRequest('auth')
            ->setPath('/auth/v2/ping')
            ->setParams(array(
                ''
            ));

        $response = $request->send();
        return ($response->success()) ? true : false;
    }

}