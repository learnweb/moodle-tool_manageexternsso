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

/**
 * A moodle form for adding new external SSO entries.
 *
 * @package    tool_manageexternsso
 * @copyright  2021 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sso_manager {

    /**
     * Add new external SSO entry.
     * @param string $username the (new) external username
     * @param int $contactid the internal moodle userid of the contactable person.
     * @param int|null $until timestamp when SSO entry will expire, or null for infinite entry.
     */
    public static function add_sso(string $username, int $contactid, ?int $until) {
        global $DB;

        $ssoentry = new \stdClass();
        $ssoentry->username_extern = $username;
        $ssoentry->userid_contact = $contactid;
        $ssoentry->until = $until;

        $DB->insert_record('tool_manageexternsso', $ssoentry);
        self::update_configfile();
    }

    /**
     * Update existing external SSO entry.
     * @param string $username the (already existing) external username
     * @param int $contactid the internal moodle userid of the contactable person.
     * @param int|null $until timestamp when SSO entry will expire, or null for infinite entry.
     */
    public static function update_sso(string $username, int $contactid, ?int $until) {
        global $DB;

        $record = $DB->get_record('tool_manageexternsso', ['username_extern' => $username]);
        if ($record == false) {
            throw new \coding_exception('Could not find specified external user in Database!');
        }

        $record->userid_contact = $contactid;
        $record->until = $until;

        $DB->update_record('tool_manageexternsso', $record);
    }


    /**
     * Delete external SSO entry by recordid.
     * @param int $ssoentryid the record id.
     */
    public static function delete_sso_by_id(int $ssoentryid) {
        global $DB;
        $DB->delete_records('tool_manageexternsso', ['id' => $ssoentryid]);
        self::update_configfile();
    }

    /**
     * Return external SSO entry by recordid.
     * @param int $ssoentryid the record id.
     */
    public static function get_sso_by_id(int $ssoentryid) {
        global $DB;
        return $DB->get_record('tool_manageexternsso', ['id' => $ssoentryid]);
    }

    /**
     * Add new external SSO entry.
     * @param string $groupname the (new) external groupname
     * @param string|null $description description for the group.
     */
    public static function add_ssogroup(string $groupname, ?string $description) {
        global $DB;

        $ssoentry = new \stdClass();
        $ssoentry->usergroup_extern = $groupname;
        $ssoentry->description = $description;

        $DB->insert_record('tool_manageexternsso_g', $ssoentry);
        self::update_configfile();
    }

    /**
     * Update existing external SSO entry.
     * @param string $groupname the (new) external groupname
     * @param string|null $description description for the group.
     */
    public static function update_ssogroup(string $groupname, ?string $description) {
        global $DB;

        $record = $DB->get_record('tool_manageexternsso_g', ['usergroup_extern' => $groupname]);
        if ($record == false) {
            throw new \coding_exception('Could not find specified external group in Database!');
        }

        $record->description = $description;

        $DB->update_record('tool_manageexternsso_g', $record);
    }


    /**
     * Delete external SSO group entry by recordid.
     * @param int $ssoentryid the record id.
     */
    public static function delete_ssogroup_by_id(int $ssoentryid) {
        global $DB;
        $DB->delete_records('tool_manageexternsso_g', ['id' => $ssoentryid]);
        self::update_configfile();
    }

    /**
     * Return external SSO group entry by recordid.
     * @param int $ssoentryid the record id.
     */
    public static function get_ssogroup_by_id(int $ssoentryid) {
        global $DB;
        return $DB->get_record('tool_manageexternsso_g', ['id' => $ssoentryid]);
    }

    /**
     * Updates the config file to match the database.
     */
    public static function update_configfile() {
        global $DB, $CFG;

        // Using postgres.
        $ssos = $DB->get_field_sql("SELECT string_agg(usergroup_extern, ' ' ORDER BY usergroup_extern ASC) " .
                "FROM {tool_manageexternsso_g} ");
        $ssos .= ' ';
        $ssos .= $DB->get_field_sql("SELECT string_agg(username_extern, ' ' ORDER BY username_extern ASC) " .
                "FROM {tool_manageexternsso} " .
                "WHERE until IS NULL OR until > :time",
                ['time' => time()]);

        $filepath = get_config('tool_manageexternsso', 'configfile');

        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new \coding_exception('Could not access .htssoaccess');
        }

        $matches = [];

        // The array matches['users'] will contain the string of users that should be replaced.
        $result = preg_match('/# tool_manageexternsso\s+Require\s+\S+\s*(?<users>\S\V*)\v/', $content, $matches, PREG_OFFSET_CAPTURE);

        if (!$result) {
            throw new \coding_exception('Could not find right place to inject user string.');
        }

        $olduserslength = strlen($matches['users'][0]);
        $olduserstart = $matches['users'][1];

        $newcontent = substr($content, 0, $olduserstart) .
            $ssos .
            substr($content, $olduserstart + $olduserslength);

        file_put_contents($CFG->dirroot . '/.htssoaccess', $newcontent);
    }

    public static function delete_expired_ssos() {
        global $DB;
        $updatenecessary = $DB->record_exists_sql(
            'SELECT id FROM {tool_manageexternsso} WHERE until IS NOT NULL AND until < :time', ['time' => time()]);

        if ($updatenecessary) {
            $DB->delete_records_select('tool_manageexternsso', 'until IS NOT NULL AND until < :time', ['time' => time()]);
            self::update_configfile();;
        }
    }

}
