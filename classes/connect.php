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
        $body_data = utils::issued_user_data_objects(false);//json parser is not necessary here

        $response = $client->post($body_data, null, null);

        if($response->statusCode() == 200) {
            $unregistred_users = json_decode($response->body());

            //filter only id fields
            $unregistred_users = array_map($callback = fn($object):int=>$object->user_id, $unregistred_users);

            $erro_ao_enviar = [];
            foreach ($body_data as $user){
                if(in_array($user->user_id, $unregistred_users)){
                    $erro_ao_enviar[] = $user;
                    continue;
                }
                //record users sent
                $idissue = utils::get_certificate_issue_id($user->user_id);
                utils::record_pnp_sent($user->user_id,$idissue);
            }
            echo "ERROS AO ENVIAR\n\n";
            var_dump($erro_ao_enviar);
        } else {
            echo "ERRO GERAL AO ENVIAR\n\n";
            var_dump($response);
        }

        return $response;

    }

}