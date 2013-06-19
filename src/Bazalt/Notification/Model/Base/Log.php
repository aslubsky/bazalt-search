<?php

namespace Bazalt\Notification\Model\Base;

/**
 * @property-read mixed id
 * @property-read mixed site_id
 * @property-read mixed address
 * @property-read mixed subject
 * @property-read mixed message
 * @property-read mixed error
 * @property-read mixed state
 * @property-read mixed created
 */
abstract class Log extends \Bazalt\ORM\Record
{
    const TABLE_NAME = 'com_notification_logs';

    const MODEL_NAME = 'Bazalt\Notification\Model\Log';

    public function __construct()
    {
        parent::__construct(self::TABLE_NAME, self::MODEL_NAME);
    }

    protected function initFields()
    {
        $this->hasColumn('id', 'PUA:int(10)');
        $this->hasColumn('site_id', 'U:int(10)');
        $this->hasColumn('address', 'varchar(255)');
        $this->hasColumn('subject', 'varchar(255)');
        $this->hasColumn('message', 'N:text');
        $this->hasColumn('error', 'N:text');
        $this->hasColumn('state', 'U:tinyint(1)');
        $this->hasColumn('created', 'datetime');
    }

    public function initRelations()
    {

    }

    public function initPlugins()
    {
    }
}