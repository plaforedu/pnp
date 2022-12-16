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
 * @author     Saulo Sá <srssaulo@gmail.com>
 * @package    block_pnp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    //dependecy ldap settingslib
    require_once($CFG->dirroot.'/enrol/ldap/settingslib.php');

    define('PLUGIN_NAME', 'local_pnp');

    $settings->add(new admin_setting_configtext(
        PLUGIN_NAME.'/certificateid',
        get_string('certificateid', BLOCK_NAME),
        get_string('certificateid_', BLOCK_NAME),
        '',
        PARAM_INT,
    ));

    $settings->add(new admin_setting_configtext_trim_lower(
        PLUGIN_NAME.'/uribase',
        get_string('uribase', PLUGIN_NAME),
        get_string('uribase_', PLUGIN_NAME),
        '',
        true,
    ));

    $settings->add(new admin_setting_configtext_trim_lower(
        PLUGIN_NAME.'/token',
        get_string('token', PLUGIN_NAME),
        get_string('token_', PLUGIN_NAME),
        '',
        false,
    ));

}

