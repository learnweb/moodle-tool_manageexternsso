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
 * Manage Extern SSOs Overview Page.
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
global $CFG, $PAGE, $OUTPUT;

require_once($CFG->libdir . '/adminlib.php');

$PAGE->set_url('/admin/tool/manageexternsso/managesso.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

$strheading = get_string('pluginname', 'tool_manageexternsso');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

require_admin();

$action = optional_param('action', null, PARAM_ALPHA);
if ($action === 'delete') {
    require_sesskey();
    $ssoentryid = required_param('ssoentryid', PARAM_INT);
    $isgroup = required_param('group', PARAM_BOOL);
    if ($isgroup) {
        \tool_manageexternsso\sso_manager::delete_ssogroup_by_id($ssoentryid);
    } else {
        \tool_manageexternsso\sso_manager::delete_sso_by_id($ssoentryid);
    }

    redirect($PAGE->url);
}
$usertable = new tool_manageexternsso\sso_table();
$grouptable = new tool_manageexternsso\ssogroup_table();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('users'));
echo $OUTPUT->single_button(new moodle_url('/admin/tool/manageexternsso/addsso.php'), get_string('addsso', 'tool_manageexternsso'), 'get');

echo "<br><br>";

$usertable->out(128, false);

echo "<br><br>";
echo $OUTPUT->heading(get_string('groups'));

echo $OUTPUT->single_button(new moodle_url('/admin/tool/manageexternsso/addssogroup.php'), get_string('addssogroup', 'tool_manageexternsso'), 'get');

echo "<br><br>";

$grouptable->out(128, false);

echo $OUTPUT->footer();

