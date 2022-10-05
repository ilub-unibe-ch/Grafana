<?php

namespace iLUB\Plugins\Grafana\Jobs;

use iLUB\Plugins\Grafana\Helper\GrafanaDBAccess;
use ilCronJob;

class DailyUsersJob extends ilCronJob
{

    const CRON_JOB_ID  = "daily_users";


    /**
     * @var \ilCronJobResult
     */
    protected $job_result;
    protected $db_access;

    /**
     * RunSync constructor.
     * @param \ilCronJobResult|null $dic_param
     * Dieses wird ausgeführt, wenn im GUI die Cron-Jobs angezeigt werden.
     */
    public function __construct(\ilCronJobResult $job_result = null, GrafanaDBAccess $db_access = null, $dic_param=null)
    {
        $this->job_result = $job_result;
        if ($this->job_result == null) {
            $this->job_result = new \ilCronJobResult();
        }
        $this->dic = $dic_param;
        if ($this->dic==null) {
            global $DIC;
            $this->dic = $DIC;
        }
        $this->db_access = $db_access;
        if ($this->db_access == null) {
            $this->db_access = new grafanaDBAccess($this->dic);
        }
    }
    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string {
        return self::CRON_JOB_ID;
    }


    /**
     * @return string
     */
    public function getTitle(): string {
        return "Grafana: Daily Users";
    }


    /**
     * @return string
     */
    public function getDescription(): string {
        return "logs how many users logged in during the last 24 hours to the database";
    }


    /**
     * @return bool
     */
    public function hasAutoActivation()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasFlexibleSchedule()
    {
        return true;
    }


    /**
     * @return null
     */
    public function getDefaultScheduleValue()
    {
        return ilCronJob::SCHEDULE_TYPE_DAILY;
    }

    /**
     * @return \ilCronJobResult
     */
    public function getJobResult()
    {

        return $this->job_result;

    }

    /**
     * @return grafanaDBAccess
     */
    public function getDBAccess()
    {

        return $this->db_access;
    }


    /**
     * @return \ilCronJobResult
     * @throws
     */
    public function run()
    {

        $jobResult = $this->getJobResult();

        try {

            $tc = $this->getDBAccess();
            $tc->logDailyUsersToDB();

            $jobResult->setStatus($jobResult::STATUS_OK);
            $jobResult->setMessage("Everything worked fine.");
            return $jobResult;
        } catch (Exception $e) {
            $jobResult->setStatus($jobResult::STATUS_CRASHED);
            $jobResult->setMessage("There was an error.");
            return $jobResult;
        }
    }

    public function getDefaultScheduleType()
    {
        return self::SCHEDULE_TYPE_DAILY;
    }
}