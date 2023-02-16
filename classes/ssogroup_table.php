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
 * External SSO Table
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_manageexternsso;

use moodle_url;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

/**
 * External SSO Table
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ssogroup_table extends \table_sql {

    /**
     * sso_table constructor.
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function __construct() {
        parent::__construct('tool_manageexternsso_overview');
        global $PAGE;

        $this->set_sql('id, usergroup_extern as usergroup, description', '{tool_manageexternsso_g}', 'true');

        $this->define_baseurl(new moodle_url($PAGE->url));
        $this->pageable(false);

        $this->define_columns(['usergroup', 'description', 'tools']);
        $this->define_headers([
            get_string('usergroup', 'tool_manageexternsso'),
            get_string('description'),
            get_string('tools', 'tool_manageexternsso')
        ]);

        $this->setup();
    }

    /**
     * Output for column tools
     * @param \stdClass $col column
     */
    public function col_tools(\stdClass $col) {
        global $OUTPUT, $PAGE;
        $delete = get_string('delete');
        $edit = get_string('edit');
        return $OUTPUT->action_icon(new \moodle_url('/admin/tool/manageexternsso/addssogroup.php',
                array('ssoentryid' => $col->id)),
                new \pix_icon('i/edit', $edit, 'moodle', array('title' => $edit)),
                null, array('title' => $edit)) .
            $OUTPUT->action_icon(new \moodle_url($PAGE->url,
                array('action' => 'delete',
                    'group' => true,
                    'sesskey' => sesskey(),
                    'ssoentryid' => $col->id)),
                new \pix_icon('i/delete', $delete, 'moodle', array('title' => $delete)),
                null, array('title' => $delete));
    }

}
