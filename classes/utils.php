<?php

namespace local_pnp;

class utils
{
    public static function get_confs_values()
    {
        $confs = [];
        $confs['certid'] = get_config('local_pnp', 'certid');
        $confs['uribase'] = get_config('local_pnp', 'uribase');
        $confs['token'] = get_config('local_pnp', 'token');
        return (object)$confs;
    }


    /**
     * Return user object content info:
     * cpf, custom field cujo nome é cpf
     * fullname, user.firstname + ' ' + user.lastname
     * timestamp_emissao_certificado, customcert_issue.timestampo
     * codigo_validacao_certificado, customcert_issue.timestampo.code
     * user_id, user.id
     * enrolment_id, user_enrolment.id  (checking if is really necessary)
     * @param $uid
     * @return object
     */
    private static function get_user_data_to_send(int $uid, $certcode, $certtimecreated): object
    {
        global $DB;

        $confs = self::get_confs_values();
        if (!$confs->certid) {
            throw new \Exception('Conf Certificate ID must exists');
        }

        $db_user = $DB->get_record('user', ['id' => $uid], 'id, firstname, lastname, email', MUST_EXIST);//exception throws

        $user = new \stdClass();
        $user->user_id = intval($db_user->id);//int casting
        $user->nome = $db_user->firstname . ' ' . $db_user->lastname;
        $user->cpf = $DB->get_record_sql(
            "SELECT uid.id, uid.data FROM {user_info_field} AS uif
                 INNER JOIN {user_info_data} AS uid ON uif.id = uid.fieldid 
                 WHERE uif.shortname = ? AND uif.datatype=? AND uid.userid = ? AND uid.data <> ''",
            ['cpf', 'text', $db_user->id])->data;//exception throws
        $user->cpf = str_pad(preg_replace('/[^0-9]/', '', $user->cpf), 11, '0', STR_PAD_LEFT); //cpf adjust

        $user->perfil_pnp = $DB->get_record_sql(
            "SELECT uid.id, uid.data FROM {user_info_field} AS uif
                 INNER JOIN {user_info_data} AS uid ON uif.id = uid.fieldid 
                 WHERE uif.shortname = ? AND uif.datatype=? AND uid.userid = ? AND uid.data <> ''",
            ['perfil_pnp', 'menu', $db_user->id])->data;//exception throws

        $user->instituicao_ept = $DB->get_record_sql(
            "SELECT uid.id, uid.data FROM {user_info_field} AS uif
                 INNER JOIN {user_info_data} AS uid ON uif.id = uid.fieldid 
                 WHERE uif.shortname = ? AND uif.datatype=? AND uid.userid = ? AND uid.data <> ''",
            ['instituicao_ept', 'autocomplete', $db_user->id])->data;//exception throws

        $user->emissao_certificado = $certtimecreated;
        $user->codigo_validacao = $certcode;
        $user->email = $db_user->email;

        return $user;
    }

    /**
     * returns users issued that was not sent
     * @param bool $json
     * @return array
     * @throws \dml_exception
     */
    public static function issued_user_data_objects(bool $json=true)
    {
        global $DB;

        $user_array_to_send = [];

        $confs = self::get_confs_values();

        if (!$confs->certid) {
            throw new \Exception('Conf Certificate ID must exists');
        }

        //only get certificates were not sent
        $certficates_issued = $DB->get_records_sql(
            "
                    SELECT ci.id, ci.userid, ci.code, ci.timecreated FROM {customcert_issues} AS ci
                    LEFT JOIN {pnp_sent} AS ps ON ci.id = ps.id_issue
                    WHERE ps.id is null AND ci.customcertid = ?
              ",
            [$confs->certid]
        );

        foreach ($certficates_issued as $issued) {
            $user_array_to_send[] = self::get_user_data_to_send(
                $issued->userid,
                $issued->code,
                $issued->timecreated
            );
        }

        if($json) {
            return json_encode($user_array_to_send);
        }
        return $user_array_to_send;

    }


    /**
     * @param int $userid
     * @return int customcert_issue id
     * @throws \dml_exception
     */
    public static function  get_certificate_issue_id(int $userid){
        global $DB;
        $confs = self::get_confs_values();
        return $DB->get_record('customcert_issues', ['userid'=>$userid, 'customcertid'=>$confs->certid], 'id')->id;
    }

    /**
     * record user that was sent
     * @param int $iduser
     * @param int $idissue
     * @return void
     * @throws \dml_exception
     */
    public static function record_pnp_sent(int $iduser, int $idissue){
        global $DB;

        //insert register
        $record = (object)[
          'id_issue'=>$idissue,
          'id_user'=>$iduser,
          'sent_time'=>time()
        ];

        $DB->insert_record('pnp_sent', $record);
    }

}
