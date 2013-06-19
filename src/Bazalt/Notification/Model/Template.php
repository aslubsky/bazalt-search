<?php

namespace Bazalt\Notification\Model;

class Template extends Base\Template
{
    public static function create($siteId)
    {
        $o = new Template();
        $o->site_id = $siteId;

        return $o;
    }
    
    public static function getByName($name)
    {
        $q = Template::select()
            ->where('name = ?', $name);

        return $q->fetchAll();
    }
    
    public static function getCollection()
    {
        $q = ORM::select('Bazalt\Notification\Model\Template n', 'n.id, n.name, n.transport')
            ->andWhere('n.site_id = ?', CMS_Bazalt::getSiteId());

        return new Bazalt\ORM\Collection($q);
    }
    
    public static function deleteByIds($ids, $siteId)
    {
        if(!is_array($ids)) {
            $ids = array($ids);
        }
        if(count($ids) == 0) {
            return;
        }
        $q = ORM::delete('Bazalt\Notification\Model\Template a')
                ->whereIn('a.id', $ids)
                ->andWhere('a.site_id = ?', (int)$siteId);

        return $q->exec();
    }
}