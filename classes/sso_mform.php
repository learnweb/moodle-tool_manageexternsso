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
class sso_mform extends \moodleform {


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

        $mform->addElement('text', 'username_extern', get_string('username'));
        $mform->setType('username_extern', PARAM_TEXT);

        $options = [
            'ajax' => 'core_search/form-search-user-selector',
            'multiple' => false,
            'noselectionstring' => get_string('noneselected', 'tool_manageexternsso'),
            'valuehtmlcallback' => function ($value) {
                global $DB, $OUTPUT;
                $user = $DB->get_record('user', ['id' => (int)$value], '*', IGNORE_MISSING);
                if (!$user || !user_can_view_profile($user)) {
                    return false;
                }
                $details = user_get_user_details($user);
                return $OUTPUT->render_from_template(
                    'core_search/form-user-selector-suggestion', $details);
            }
        ];
        $mform->addElement('autocomplete', 'contactuser', get_string('contact', 'tool_manageexternsso'), array(), $options);
        $mform->addRule('contactuser', get_string('required'), 'required');

        $mform->addElement('date_time_selector', 'until', get_string('until', 'tool_manageexternsso'));
        $mform->disabledIf('until', 'infinite', 'checked');

        $mform->addElement('checkbox', 'infinite', get_string('infinite_questionmark', 'tool_manageexternsso'));

        if ($this->ssoentry) {
            $mform->addElement('hidden', 'ssoentryid', $this->ssoentry->id);
            $mform->setType('ssoentryid', PARAM_INT);
            $mform->setDefault('username_extern', $this->ssoentry->username_extern);
            $mform->setDefault('contactuser', $this->ssoentry->userid_contact);
            $mform->setDefault('infinite', $this->ssoentry->until == null);
            if ($this->ssoentry->until != null) {
                $mform->setDefault('until', $this->ssoentry->until);
            }
            $mform->freeze(['username_extern']);
        } else {
            $mform->addRule('username_extern', get_string('required'), 'required');
            $mform->setDefault('until', time() + 60 * 60 * 24 * 182);
        }

        $this->add_action_buttons();
    }

}
