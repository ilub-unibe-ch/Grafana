# Description Grafana Plugin
This ILIAS-Plugin is supposed to be used together with a Grafana Instance.
It  will create Database-Tables that will be used to display important stats of the ILIAS Installation in Grafana.

## Installation

### Install Grafana plugin
Start at your ILIAS root directory 

```
mkdir -p Customizing/global/plugins/Services/Cron/CronHook
cd Customizing/global/plugins/Services/Cron/CronHook
git clone git@github.com:ilub-unibe-ch/Grafana.git
```


## Cron Jobs

### Activate Cron Jobs
This is a Cron Plugin so in order for it to work Cron-jobs need to be activated. This can be done in the folder /etc/cron.d
create a file named ilias in /etc/cron.d with the following content

```
*/15 * * * * www-data /usr/bin/php ILIAS_PATH_ABSOLUTE/cron/cron.php ADMIN-USER ADMIN-USER-PWD ILIAS_CLIENT_ID
```
 Using this configuration, cron jobs will be executed every 15 minutes.
 For more Information visit this link: https://docu.ilias.de/goto_docu_pg_8240_367.html
 
### Configure Cron Job in ILIAS Administration
All the Cron Jobs can be found under Administration->General Settings->Cron Jobs.
Look for pl__Grafana. Make sure that this Cron Job is activated.
Edit the configuration and decide how often the Grafana-Cron-Job will be executed.





