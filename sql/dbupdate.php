<#1>
<?php
/** @var ilDB $ilDB */
global $ilDB;
$db = $ilDB;
require_once('Customizing/global/plugins/Services/Cron/CronHook/Grafana/classes/class.ilGrafanaPlugin.php');

if (!$db->tableExists(ilGrafanaPlugin::TABLE_NAME)) {
    $fields = array(
        'metric' => array(
            'type'    => 'text',
            'length'  => 8,
            'notnull' => true
        ),

        'enabled' => array(
        'type'    => 'integer',
        'length'  => 1,
		'default' =>0
    )
    );
    $db->createTable(ilGrafanaPlugin::TABLE_NAME, $fields);
    $db->addPrimaryKey(ilGrafanaPlugin::TABLE_NAME, array('metric'));
}
?>
<#2>
<?php
/** @var ilDB $ilDB */
global $ilDB;
$db = $ilDB;
require_once('Customizing/global/plugins/Services/Cron/CronHook/Grafana/classes/class.ilGrafanaPlugin.php');
if (!$db->tableExists(ilGrafanaPlugin::SES_LOG_TABLE)) {
    $fields = array(
        'timestamp'              => array(
            'type'    => 'integer',
            'length'  => '4',
            'notnull' => true
        ),
        'date'                   => array(
            'type'    => 'timestamp',
            'notnull' => true
        ),
        'all_remaining_sessions' => array(
            'type'   => 'integer',
            'length' => '4'
        ),
        'active_during_last_5min'=> array(
            "type"   => "integer",
            "length" => '4'
		),
        'active_during_last_15min'=> array(
            "type"   => "integer",
            "length" => '4'
		),
        'active_during_last_hour' =>array(
            "type"   => "integer",
            "length" => '4'
		)
    );
    $db->createTable(ilGrafanaPlugin::SES_LOG_TABLE, $fields);
    $db->addPrimaryKey(ilGrafanaPlugin::SES_LOG_TABLE, array('timestamp'));

}
?>