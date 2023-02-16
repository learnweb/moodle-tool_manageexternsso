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
 * A moodle form for adding new external SSO entries.
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_manageexternsso;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/formslib.php');

/**
 * A moodle form for adding new external SSO entries.
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ssogroup_mform extends \moodleform {


    /**
     * @var \stdClass
     */
    public $ssoentry;

    /**
     * Constructor
     * @param \stdClass $ssoentry ssoentry or null.
     */
    public function __construct($ssoentry) {
        $this->ssoentry = $ssoentry;

        parent::__construct();
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'usergroup_extern', get_string('usergroup', 'tool_manageexternsso'));
        $mform->setType('usergroup_extern', PARAM_TEXT);

        $mform->addElement('text', 'description', get_string('description'));
        $mform->setType('description', PARAM_TEXT);


        if ($this->ssoentry) {
            $mform->addElement('hidden', 'ssoentryid', $this->ssoentry->id);
            $mform->setType('ssoentryid', PARAM_INT);
            $mform->setDefault('usergroup_extern', $this->ssoentry->usergroup_extern);
            $mform->setDefault('description', $this->ssoentry->description);
            $mform->freeze(['usergroup_extern']);
        } else {
            $mform->addRule('usergroup_extern', get_string('required'), 'required');
        }

        $this->add_action_buttons();
    }

}
