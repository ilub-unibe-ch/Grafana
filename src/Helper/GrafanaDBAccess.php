<?php
declare(strict_types=1);
namespace iLUB\Plugins\Grafana\Helper;

/**
 * Class GrafanaDBAccess
 * This class is responsible for the interaction between the database and the plugin
 */
use ilDB;
use ilGrafanaPlugin;
use ILIAS\DI\Container;

class GrafanaDBAccess implements GrafanaDBInterface
{

    protected \ilDBInterface $db;


    protected Container $DIC;

    /**
     * @throws \Exception
     */

    public function __construct(Container $dic_param = null, \ilDBInterface $db_param = null)
    {

        if ($dic_param == null) {
            global $DIC;
            $this->DIC = $DIC;
        } else {
            $this->DIC = $dic_param;
        }
        if ($db_param == null) {
            $this->db = $this->DIC->database();
        } else {
            $this->db = $db_param;
        }
    }

    /**
     * Removes the table from DB after uninstall is triggered.
     */
    public function removePluginTableFromDB(): void
    {
        $sql = "DROP TABLE " . ilGrafanaPlugin::TABLE_NAME;
        $this->db->query($sql);

        $sql = "DROP TABLE " . ilGrafanaPlugin::SES_LOG_TABLE;
        $this->db->query($sql);

        $sql = "DROP TABLE " . ilGrafanaPlugin::DAILY_USERS_TABLE;
        $this->db->query($sql);
    }


    public function logSessionsToDB(): void
    {
        $timestamp = time();
        $this->db->insert('grafana_ses_log', array(
            'timestamp'                => array('integer', $timestamp),
            'date'                     => array('datetime', date('Y-m-d H:i:s', $timestamp)),
            'all_remaining_sessions'   => array('integer', $this->getAllSessions()),
            'active_during_last_5min'  => array('integer', $this->getUsersActiveBetween($timestamp - 300, $timestamp)),
            'active_during_last_15min' => array('integer', $this->getUsersActiveBetween($timestamp - 900, $timestamp)),
            'active_during_last_hour'  => array('integer', $this->getUsersActiveBetween($timestamp - 3600, $timestamp))
        ));
    }

    public function logDailyUsersToDB(): void
    {
        $timestamp = time();
        $this->db->insert("grafana_daily_user", array(
            'date'                     => array('datetime', date('Y-m-d H:i:s', $timestamp)),
            'daily_users'              => array('integer', $this->getUsersLoggedInToday($timestamp))
        ));

    }

    /**
     * @return mixed
     */
    public function getAllSessions(): int
    {
        $sql   = "SELECT count(*) FROM usr_session";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);

        return (int) $rec['count(*)'];
    }

    /**
     * @param $timeEarly
     * @param $timeLate
     * @return mixed
     */
    public function getUsersActiveBetween(int $timeEarly, int $timeLate): int
    {
        $sql   = "SELECT count(distinct usr_session.user_id) from usr_session where ctime Between '" . $timeEarly . "'and '" . $timeLate . "'";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);
        return (int) $rec['count(distinct usr_session.user_id)'];
    }


    public function getUsersLoggedInToday(int $timestamp): int
    {
            $today = date('Y-m-d H:i:s', $timestamp- 86400);
            $sql = "SELECT count(usr_data.usr_id) from usr_data where last_login >= '" . $today . "'";
            $query = $this->db->query($sql);
            $rec = $this->db->fetchAssoc($query);
            return (int) $rec['count(usr_data.usr_id)'];
    }


}