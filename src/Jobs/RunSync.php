<?php
declare(strict_types=1);
namespace iLUB\Plugins\Grafana\Jobs;

use Exception;
use ilCronJob;
use iLUB\Plugins\Grafana\Helper\GrafanaDBAccess;
use ILIAS\Cron\Schedule\CronJobScheduleType;

/**
 * Class RunSync
 * This class has to run the Cron Job
 * @package iLUB\Plugins\Grafana\Jobs
 */
class RunSync extends ilCronJob
{

    const CRON_JOB_ID  = "sess_log";
    /**
     * @var
     */
    protected $dic;

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
    public function __construct(
        \ilCronJobResult $job_result = null,
        GrafanaDBAccess $db_access = null,
        $dic_param = null
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

    public function getId(): string
    {
        return self::CRON_JOB_ID;
    }

    public function hasAutoActivation(): bool
    {
        return true;
    }

    public function hasFlexibleSchedule(): bool
    {
        return true;
    }

    public function getDefaultScheduleType(): CronJobScheduleType
    {
        return CronJobScheduleType::SCHEDULE_TYPE_DAILY;
    }

    public function getDefaultScheduleValue(): int
    {
        return 1;
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
            $tc->logsessionsToDB();

            $jobResult->setStatus($jobResult::STATUS_OK);
            $jobResult->setMessage("Everything worked fine.");
            return $jobResult;
        } catch (Exception $e) {
            $jobResult->setStatus($jobResult::STATUS_CRASHED);
            $jobResult->setMessage("There was an error.");
            return $jobResult;
        }
    }

    public function getTitle() : string
    {
        return "Grafana Log";
    }

    public function getDescription() : string
    {
        return " creates DB-Log to show how many users are active in grafana";
    }
}
