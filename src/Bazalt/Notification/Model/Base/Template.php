<?php

namespace Bazalt\Notification\Model\Base;

/**
 * @property-read mixed id
 * @property-read mixed component_id
 * @property-read mixed site_id
 * @property-read mixed transport
 * @property-read mixed name
 * @property-read mixed data
 */
abstract class Template extends \Bazalt\ORM\Record
{
    const TABLE_NAME = 'com_notification_templates';

    const MODEL_NAME = 'Bazalt\Notification\Model\Template';

    public function __construct()
    {
        parent::__construct(self::TABLE_NAME, self::MODEL_NAME);
    }

    protected function initFields()
    {
        $this->hasColumn('id', 'PUA:int(10)');
        $this->hasColumn('component_id', 'U:int(10)');
        $this->hasColumn('site_id', 'U:int(10)');
        $this->hasColumn('transport', 'int(10)');
        $this->hasColumn('name', 'varchar(255)');
        $this->hasColumn('data', 'blob');
    }

    public function initRelations()
    {
    }

    public function initPlugins()
    {
        $this->hasPlugin('Bazalt\ORM\Plugin\Serializable', 'data');
    }
}