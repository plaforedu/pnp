<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * log_pnp scheduled task.
 *
 * @package    local
 * @subpackage pnp
 * @copyright  2022 Saulo Sá <srssaulo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_pnp\task;
defined('MOODLE_INTERNAL') || die();

use local_pnp\connect;

class CertficateSendTask extends \core\task\scheduled_task
{

    /**
     * @inheritDoc
     */
    public function get_name()
    {
        // TODO: Implement get_name() method.
        return get_string('CertficateSendTask', 'local_pnp');
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        // TODO: Implement execute() method.
        // TODO: lê os certificados que ainda não foram enviados para um entrypoint
        $connect = new connect();
        $response = $connect->sent_data();

        //TODO register all success user registered
    }
}