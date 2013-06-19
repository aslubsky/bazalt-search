<?php
/**
 * ElasticaPlugin.php
 *
 * @category   System
 * @package    ORM
 * @subpackage Plugin
 * @copyright  2010 Equalteam
 * @license    GPLv3
 * @version    $Revision: 133 $
 */

namespace Bazalt\Seaerch;

use Bazalt\ORM as ORM;

/**
 * ElasticaPlugin 
 * Плагін, що надає змогу автоматично серіалізувати поля в базі даних 
 * @link http://wiki.bazalt.org.ua/ORMSerializable
 *
 * @category   System
 * @package    ORM
 * @subpackage Plugin
 * @copyright  2010 Equalteam
 * @license    GPLv3
 * @version    $Revision: 133 $
 */
class ElasticaPlugin extends ORM\Plugin\AbstractPlugin
{
    /**
     * Ініціалізує плагін
     * 
     * @param ORM\Record $model   Модель, для якої викликано initFields
     * @param array      $options Масив опцій, передається з базової моделі при ініціалізації плагіна
     *
     * @return void 
     */
    public function init(ORM\Record $model, $options)
    {
        ORM\BaseRecord::registerEvent($model->getModelName(), ORM\BaseRecord::ON_RECORD_SAVE, array($this,'onSave'));
    }


    /**
     *
     *
     * @param Record $record  Поточний запис
     * @param bool       &$return Флаг, який зупиняє подальше виконання save()
     *
     * @return void
     */
    public function onSave(Record $record, &$return)
    {
        $options = $this->getOptions();
        if (!array_key_exists($record->getModelName(), $options)) {
            return;
        }
        $options = $options[$record->getModelName()];

        $elasticaClient = new \Elastica\Client(array(
            'url' => 'http://experiments.equalteam.net:9200/',
        ));

        $index = $elasticaClient->getIndex('news.mistinfo.com');
        $type = $index->getType('news');
        $newsDoc = new \Elastica\Document($record->id, $record->toArray());

        if($record->isPKEmpty()) {
            $type->createDocument($newsDoc);
        } else {
            $type->updateDocument($newsDoc);
        }
        $index->refresh();
//        if(array_key_exists('created', $options) && $record->isPKEmpty()) {
//            $record->{$options['created']} = gmdate('Y-m-d H:i:s');
//        }
//        if(array_key_exists('updated', $options)) {
//            $record->{$options['updated']} = gmdate('Y-m-d H:i:s');
//        }
    }
}