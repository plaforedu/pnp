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
    public static function get_user_data_to_send(int $uid): object
    {
        global $DB;

        $confs = self::get_confs_values();
        if (!$confs->certid) {
            throw new \Exception('Conf Certificate ID must exists');
        }

        $db_user = $DB->get_record('user', ['id' => $uid], 'id, firstname, lastname', MUST_EXIST);//exception throws

        $user = new \stdClass();
        $user->user_id = $db_user->id;
        $user->fullname = $db_user->firstname . ' ' . $db_user->lastname;
        $user->cpf = $DB->get_record_sql(
            "SELECT uid.id, uid.data FROM {user_info_field} AS uif
                 INNER JOIN {user_info_data} AS uid ON uif.id = uid.fieldid 
                 WHERE uif.shortname = ? AND uif.datatype=? AND uid.userid = ? ",
            ['cpf', 'text', $db_user->id],
            MUST_EXIST)->data;//exception throws

        //certificate issue data. User only generate unique certificate using specific certificate id
        $cert_issued = $DB->get_record('customcert_issues', ['customcertid' => $confs->certid, 'userid' => $db_user->id], 'id, code, timecreated');

        $user->emissao_certificado = $cert_issued->timecreated;
        $user->codigo_validacao = $cert_issued->code;

        return $user;
    }


}