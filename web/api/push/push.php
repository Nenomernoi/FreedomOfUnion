<?php

/**
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class Push {

    // push message title
    private $title;
    private $message;
    private $type;
    private $body;

    function __construct() {
        
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setData($body) {
        $this->body = $body;
    }

    public function getPush() {
        $res = array();

        $res['data']['title'] = $this->title;
        $res['data']['message'] = $this->message;
        $res['data']['type'] = $this->type;
        $res['data']['body'] = $this->body;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');

        return $res;
    }

}
