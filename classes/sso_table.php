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
class sso_table extends \table_sql {

    /**
     * sso_table constructor.
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function __construct() {
        parent::__construct('tool_manageexternsso_overview');
        global $PAGE;
        $fields = "sso.id, sso.username_extern as username, sso.userid_contact as contact, sso.until ";
        $fields .= \core_user\fields::for_name()->get_sql('cu', false, '')->selects;

        $from = '{tool_manageexternsso} sso ' .
            'JOIN {user} cu ON cu.id = sso.userid_contact';

        $this->set_sql($fields, $from, 'true');

        $this->define_baseurl(new moodle_url($PAGE->url));
        $this->pageable(false);

        $this->define_columns(['username', 'contact', 'until', 'tools']);
        $this->define_headers([
            get_string('externusername', 'tool_manageexternsso'),
            get_string('contact', 'tool_manageexternsso'),
            get_string('until', 'tool_manageexternsso'),
            get_string('tools', 'tool_manageexternsso')
        ]);

        $this->setup();
    }

    /**
     * Output for column contact
     * @param \stdClass $col column
     */
    public function col_contact(\stdClass $col) {
        return \html_writer::link(new moodle_url('/user/profile.php', ['id' => $col->contact]), fullname($col));
    }

    /**
     * Output for column until
     * @param \stdClass $col column
     */
    public function col_until(\stdClass $col) {
        if ($col->until === null) {
            return get_string('infinite', 'tool_manageexternsso');
        } else {
            return userdate($col->until);
        }
    }

    /**
     * Output for column tools
     * @param \stdClass $col column
     */
    public function col_tools(\stdClass $col) {
        global $OUTPUT, $PAGE;
        $delete = get_string('delete');
        $edit = get_string('edit');
        return $OUTPUT->action_icon(new \moodle_url('/admin/tool/manageexternsso/addsso.php',
                array('ssoentryid' => $col->id)),
                new \pix_icon('i/edit', $edit, 'moodle', array('title' => $edit)),
                null, array('title' => $edit)) .
            $OUTPUT->action_icon(new \moodle_url($PAGE->url,
                array('action' => 'delete',
                    'sesskey' => sesskey(),
                    'ssoentryid' => $col->id)),
                new \pix_icon('i/delete', $delete, 'moodle', array('title' => $delete)),
                null, array('title' => $delete));
    }

}
