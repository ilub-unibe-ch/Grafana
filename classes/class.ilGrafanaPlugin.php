<?php
declare(strict_types=1);

use iLUB\Plugins\Grafana\Helper\GrafanaDBAccess;
use iLUB\Plugins\Grafana\Jobs\RunSync;
use iLUB\Plugins\Grafana\Jobs\DailyUsersJob;


/**
 * Class ilGrafanaPlugin
 * @package
 */
class ilGrafanaPlugin extends ilCronHookPlugin
{

    const PLUGIN_ID = 'grafana';
    const PLUGIN_NAME = 'Grafana';
    const TABLE_NAME = 'grafana_config';
    const SES_LOG_TABLE = 'grafana_ses_log';
    const DAILY_USERS_TABLE = 'grafana_daily_user';


    protected static ilGrafanaPlugin $instance;
    protected GrafanaDBAccess $db_access;

    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    public static function getInstance() : ilGrafanaPlugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return ilCronJob[]
     */
    public function getCronJobInstances() : array
    {

        return [new RunSync(), new DailyUsersJob()];
    }

    public function getCronJobInstance(string $runSync) : ilCronJob
    {
        switch ($runSync) {
            case RunSync::CRON_JOB_ID:
                return new RunSync();

            case DailyUsersJob::CRON_JOB_ID:
                return new DailyUsersJob();
        }
    }

    /**
     * AfterUninstall deletes the tables from the DB
     */
    protected function afterUninstall(): void
    {
        $this->db_access = new GrafanaDBAccess();
        $this->db_access->removePluginTableFromDB();
    }

}
