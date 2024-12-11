<?php
declare(strict_types=1);
namespace iLUB\Plugins\Grafana\Helper;

interface GrafanaDBInterface
{

    /**
     * Removes the table from DB after uninstall is triggered.
     */
    public function removePluginTableFromDB();

    /**
     * logs to database: how many anonymous sessions were deleted and how many sessions were active in the last 5/15/60 Minutes
     */

    public function logSessionsToDB();

    /**
     * returns the count of all  active sessions
     * @return  mixed
     */
    public function getAllSessions(): int;

    /**
     * returns how many Users were active during the two timestamps
     * @param $timeEarly
     * @param $timeLate
     * @return mixed
     */
    public function getUsersActiveBetween(int $timeEarly, int $timeLate): int;
}