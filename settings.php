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
 * Settings for manage extern SSO.
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('tools', new admin_category('tool_manageexternsso_category',
        new lang_string('pluginname', 'tool_manageexternsso')));

    $ADMIN->add('tool_manageexternsso_category', new admin_externalpage('tool_manageexternsso_manage',
        new lang_string('overview', 'tool_manageexternsso'),
        new moodle_url('/admin/tool/manageexternsso/managesso.php')));

    $settingspage = new admin_settingpage('tool_manageexternsso_settings', new lang_string('settings'));

    if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_configfile('tool_manageexternsso/configfile',
            new lang_string('configfile', 'tool_manageexternsso'),
            null, $CFG->dirroot . '/../.htssoaccess'));
    }

    $ADMIN->add('tool_manageexternsso_category', $settingspage);
}
