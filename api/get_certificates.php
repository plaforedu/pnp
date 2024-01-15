<?php

namespace local_pnp;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Exemplo: curl -H "Authentication: Token changeme" http://ava/local/pnp/api/?get_certificates&cpf=12345678901


class get_certificates_service extends service
{

    function do_call()
    {
        global $CFG, $DB;
        $ids = \get_config('local_pnp', 'certid');
        $notas = $DB->get_records_sql("
            SELECT   c.name                           AS certificado,
                     i.code                           AS comprovante,
                     to_timestamp(i.timecreated)      AS emitido_em
            FROM     {customcert_issues}              AS i
                         INNER JOIN {customcert}      AS c ON (i.customcertid = c.id)
                         INNER JOIN {user_info_data}  AS d ON (i.userid = d.userid)
                         INNER JOIN {user_info_field} AS f ON (d.fieldid = f.id AND f.shortname = 'cpf')
            WHERE    i.customcertid IN ($ids)
              AND    d.data = ?
        ", [$_GET['cpf']]);
        return array_values($notas);
    }
}
