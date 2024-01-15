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

$ADMIN->add('localplugins', new admin_category('local_pnp', get_string('pluginname', 'local_pnp')));
$page = new admin_settingpage('pnpconfs', get_string('pnpconfs', 'local_pnp'));
//dependecy ldap settingslib
require_once($CFG->dirroot . '/enrol/ldap/settingslib.php');


$page->add(new admin_setting_configtext(
    'local_pnp' . '/certid',
    get_string('certid', 'local_pnp'),
    get_string('certid_', 'local_pnp'),
    '',
    true,
));

$page->add(new admin_setting_configtext_trim_lower(
    'local_pnp' . '/uribase',
    get_string('uribase', 'local_pnp'),
    get_string('uribase_', 'local_pnp'),
    '',
    true,
));

$page->add(new admin_setting_configtext_trim_lower(
    'local_pnp' . '/token',
    get_string('token', 'local_pnp'),
    get_string('token_', 'local_pnp'),
    '',
    false,
));

$page->add(new admin_setting_configtext_trim_lower(
    'local_pnp' . '/auth_token',
    get_string('auth_token', 'local_pnp'),
    get_string('auth_token_', 'local_pnp'),
    '',
    false,
));

$ADMIN->add('local_pnp', $page);
