<?php
declare(strict_types=1);
namespace iLUB\Plugins\Grafana\Jobs;

use iLUB\Plugins\Grafana\Helper\GrafanaDBAccess;
use ilCronJob;
use ILIAS\DI\Container;

class DailyUsersJob extends ilCronJob
{

    const CRON_JOB_ID  = "daily_users";

    protected \ilCronJobResult $job_result;
    protected GrafanaDBAccess $db_access;
    protected Container $dic;

    /**
     * RunSync constructor.
     * Dieses wird ausgeführt, wenn im GUI die Cron-Jobs angezeigt werden.
     */
    public function __construct(
        \ilCronJobResult $job_result = null,
        GrafanaDBAccess $db_access = null,
        Container $dic_param = null
    ) {
        if ($job_result == null) {
            $this->job_result = new \ilCronJobResult();
        } else {
            $this->job_result = $job_result;
        }
        if ($dic_param == null) {
            global $DIC;
            $this->dic = $DIC;
        } else {
            $this->dic = $dic_param;
        }
        if ($db_access == null) {
            $this->db_access = new grafanaDBAccess($this->dic);
        } else {
            $this->db_access = $db_access;
        }
    }


    public function getId(): string {
        return self::CRON_JOB_ID;
    }


    public function getTitle(): string {
        return "Grafana: Daily Users";
    }

    public function getDescription(): string {
        return "logs how many users logged in during the last 24 hours to the database";
    }

    public function hasAutoActivation(): bool
    {
        return true;
    }

    public function hasFlexibleSchedule(): bool
    {
        return true;
    }

    public function getDefaultScheduleValue(): int
    {
        return ilCronJob::SCHEDULE_TYPE_DAILY;
    }

    public function getJobResult(): \ilCronJobResult
    {
        return $this->job_result;
    }

    public function getDBAccess(): GrafanaDBAccess
    {
        return $this->db_access;
    }

    public function run(): \ilCronJobResult
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

    public function getDefaultScheduleType(): int
    {
        return self::SCHEDULE_TYPE_DAILY;
    }
}