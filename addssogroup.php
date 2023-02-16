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
 * Add Extern SSOs group Overview Page.
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_manageexternsso\sso_manager;

require_once(__DIR__ . '/../../../config.php');
global $CFG, $PAGE, $OUTPUT;

require_once($CFG->libdir . '/adminlib.php');

$ssoentryid = optional_param('ssoentryid', null, PARAM_INT);

$PAGE->set_url(new moodle_url('/admin/tool/manageexternsso/addssogroup.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

$strheading = get_string('addssogroup', 'tool_manageexternsso');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

require_admin();

$ssoentry = null;
if ($ssoentryid !== null) {
    $ssoentry = sso_manager::get_ssogroup_by_id($ssoentryid);
}

$mform = new tool_manageexternsso\ssogroup_mform($ssoentry);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/manageexternsso/managesso.php'));
}

if ($data = $mform->get_data()) {
    if (property_exists($data, 'ssoentryid')) {
        sso_manager::update_ssogroup($data->usergroup_extern, $data->description);
    } else {
        sso_manager::add_ssogroup($data->usergroup_extern, $data->description);
    }

    redirect(new moodle_url('/admin/tool/manageexternsso/managesso.php'));
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();

