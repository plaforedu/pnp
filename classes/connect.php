<?php

namespace local_pnp;

require_once ($CFG->dirroot.'/local/pnp/vendor/autoload.php');
use SendGrid\Client;
class connect
{
    private $token;
    private $uribase;
    private $certid;

    public function __construct()
    {
        $confs = utils::get_confs_values();
        if(!$confs->token){
            throw new \Exception('Token must exists');
        }

        if(!$confs->uribase){
            throw new \Exception('URI Base must exists');
        }

        $this->uribase = $confs->uribase;
        $this->token = $confs->token;
        $this->certid = $confs->certid;
    }

    public function sent_data(){
        //DOCS: https://packagist.org/packages/sendgrid/php-http-client
        $authHeaders = [
            'Authorization: Bearer ' .  $this->token
        ];



        $client = new Client($this->uribase, $authHeaders);
        $body_data = utils::issued_user_data_objects(false);

//        print_object($body_data); die;

        $response = $client->post($body_data, null, null);

        //TODO register all success user registered
        return $response;

    }

}