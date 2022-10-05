<?php

namespace iLUB\Plugins\Grafana\Helper;

/**
 * Class GrafanaDBAccess
 * This class is responsible for the interaction between the database and the plugin
 */
use ilDB;
use ilGrafanaPlugin;

class GrafanaDBAccess implements GrafanaDBInterface
{

    /**
     * @var ilDB
     */
    protected $db;

    protected $all_remaining_sessions;



    /**
     * @var DIC
     */
    protected $DIC;

    /**
     * GrafanaDBAccess constructor.
     * @param      $dic
     * @param null $db
     * @throws \Exception
     */

    public function __construct($dic_param = null, $db_param = null)
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
    public function removePluginTableFromDB()
    {
        $sql = "DROP TABLE " . ilGrafanaPlugin::TABLE_NAME;
        $this->db->query($sql);

        $sql = "DROP TABLE " . ilGrafanaPlugin::SES_LOG_TABLE;
        $this->db->query($sql);

        $sql = "DROP TABLE " . ilGrafanaPlugin::DAILY_USERS_TABLE;
        $this->db->query($sql);
    }


    public function logsessionsToDB()
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

    public function logDailyUsersToDB()
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
    public function getAllSessions()
    {
        $sql   = "SELECT count(*) FROM usr_session";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);

        return $rec['count(*)'];
    }

    /**
     * @param $timeEarly
     * @param $timeLate
     * @return mixed
     */
    public function getUsersActiveBetween($timeEarly, $timeLate)
    {
        $sql   = "SELECT count(distinct usr_session.user_id) from usr_session where ctime Between '" . $timeEarly . "'and '" . $timeLate . "'";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);
        return $rec['count(distinct usr_session.user_id)'];
    }


    public function getUsersLoggedInToday($timestamp){
            $today = date('Y-m-d H:i:s', $timestamp- 86400);
            $sql = "SELECT count(usr_data.usr_id) from usr_data where last_login >= '" . $today . "'";
            $query = $this->db->query($sql);
            $rec = $this->db->fetchAssoc($query);
            return $rec['count(usr_data.usr_id)'];
    }


}