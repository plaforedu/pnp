<?php


/**
 * Definition of local_pnp tasks.
 *
 * @package    local_pnp
 * @category   local
 * @copyright  2022 Saulo Sá <srssaulo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_pnp\task\CertificateSendTask',
        'blocking' => 0,
        'minute' => '01',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ),
);
