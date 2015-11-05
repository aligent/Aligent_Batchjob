Aligent Batchjob Extension
=====================

Facts
-----
- version: 0.0.1
- extension key: Aligent_Batchjob

Description
-----------
This module provides a batch processing fromework for Magento.  It provides a boilerplate around which unit testable batch jobs can be built for things like file imports/exports can be built.  This work was inspired by [Akeneo's BatchBundly Symfony component]{https://github.com/akeneo/BatchBundle} which was in turn inspired by [Spring Batch]{http://docs.spring.io/spring-batch/reference/html/domain.html}.

Taxonomy:

* **Jobs** - Represent a process that should be run as an atomic unit (e.g. from a cron job).
* **Step** - A job may consist of one of more steps which are run in sequence.  A step may return false to cancel the processing of all subsequent steps. Steps should be used to represent high level processes within a Job. e.g. An import process could be broken up into multiple steps:
  1. Fetch a file from SFTP.  
  2. Import the file into Magento.
  3. Delete the file once imported.
* **ItemTask** - A step may be broken up into one of more item tasks.  ItemTasks are run in sequence, once per logical record.  Individual ItemTasks may perform transformations on a record, or save it to the database, etc.  An ItemTask may return false to prevent further processing of itemtasks for that logical record.

Also see the `<batchJobs>` structure in config.xml to see how Jobs/Steps and Tasks are wired together.


Config XML for Transports
=========================

This is the system.xml code required to define the system config values used by the transport classes (SFTP and local file).  `config_set` refers to the name of a group of configuration settings (different batch jobs might require different transports.

```xml
                <config_set translate="label comment" module="batchjob">
                    <label>Name of COnfig Set Here</label>
                    <show_in_default>1</show_in_default>
                    <show_in_website>1</show_in_website>
                    <show_in_store>1</show_in_store>
                    <sort_order>9998</sort_order>
                    <fields>
                        <transport translate="label comment">
                            <label>Transport</label>
                            <comment>Connection type used for file transport</comment>
                            <frontend_type>select</frontend_type>
                            <source_model>batchjob/system_config_source_transport</source_model>
                            <sort_order>10</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                        </transport>
                        <sftp_hostname translate="label comment">
                            <label>SFTP Host Name:</label>
                            <frontend_type>text</frontend_type>
                            <sort_order>20</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                            <depends>
                                <transport>sftp</transport>
                            </depends>
                        </sftp_hostname>
                        <sftp_username translate="label comment">
                            <label>SFTP Username:</label>
                            <frontend_type>text</frontend_type>
                            <sort_order>30</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                            <depends>
                                <transport>sftp</transport>
                            </depends>
                        </sftp_username>
                        <sftp_password translate="label comment">
                            <label>SFTP Password:</label>
                            <frontend_type>obscure</frontend_type>
                            <backend_model>adminhtml/system_config_backend_encrypted</backend_model>
                            <sort_order>40</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                            <depends>
                                <transport>sftp</transport>
                            </depends>
                        </sftp_password>
                        <sftp_fetch_path translate="label comment">
                            <label>SFTP Fetch Path:</label>
                            <frontend_type>text</frontend_type>
                            <sort_order>50</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                            <depends>
                                <transport>sftp</transport>
                            </depends>
                        </sftp_fetch_path>
                        <local_fetch_path translate="label comment">
                            <label>Local Fetch Path:</label>
                            <comment>Absolute paths (paths beginning with a /) are from the root of the file system.  Relative paths are relative to the Magento base directory.</comment>
                            <frontend_type>text</frontend_type>
                            <sort_order>70</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                            <depends>
                                <transport>local</transport>
                            </depends>
                        </local_fetch_path>
                        <archive_path translate="label comment">
                            <label>Archive Path:</label>
                            <comment>Absolute paths (paths beginning with a /) are from the root of the file system.  Relative paths are relative to the Magento base directory.</comment>
                            <frontend_type>text</frontend_type>
                            <sort_order>90</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                        </archive_path>
                    </fields>
                </config_set>
```

Example Config XML for a simple CSV import
==========================================

```xml
    <batchJobs>
        <importCsv>
            <model>batchjob/job_csv</model>
            <delimiter>,</delimiter>
            <enclosure>"</enclosure>
            <escape>\</escape>

            <steps>
                <fetch>
                    <model>batchjob/step_fetchFile</model>
                    <fileType>filespec</fileType>
                    <configSet>config_set</configSet>
                </fetch>
                <open>
                    <model>batchjob/step_openCsv</model>
                </open>
                <importData>
                    <model>batchjob/step_iterateCsv</model>
                    <itemTasks>
                        <translate>
                            <model>custommodule/importcsv_translate</model>
                        </translate>
                        <save>
                            <model>custommodule/importcsv__save</model>
                        </save>
                    </itemTasks>
                </importCsv>
                <close>
                    <model>batchjob/step_closeCsv</model>
                </close>
                <archive>
                    <model>batchjob/step_archiveFile</model>
                    <configSet>config_set</configSet>
                </archive>
            </steps>
        </importCsv>
    </batchJobs>
```

Installation Instructions
-------------------------
1. Install this module via modman or composer

Uninstallation
--------------
1. Delete .modman/Aligent_Batchjob and run "modman repair", or remove it from your composer.json file.

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/aligent/Aligent_Batchjob/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Jim O'Halloran <jim@aligent.com.au>

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2015 Aligent Consulting
