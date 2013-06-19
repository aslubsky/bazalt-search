<?php

namespace Bazalt\Notification\Model;

class Log extends Base\Log
{
    const STATE_PLANED = 3;

    const STATE_SEND_FAILED = 0;

    const STATE_SEND_SUCCESS = 1;

    const STATE_RECIVED = 2;

    public static function getCurrentTableName()
    {
        return self::TABLE_NAME . '_current';
    }

    public static function getArchiveTableName()
    {
        return self::TABLE_NAME . '_archive';
    }

    public static function getStates()
    {
        return array(
            self::STATE_PLANED => __('Planed', ComNotification::getName()),
            self::STATE_SEND_FAILED => __('Send failed', ComNotification::getName()),
            self::STATE_SEND_SUCCESS => __('Sent successfully', ComNotification::getName()),
            self::STATE_RECIVED => __('Received', ComNotification::getName())
        );
    }

    public function getState()
    {
        $states = self::getStates();
        return $states[$this->state];
    }

    public static function create()
    {
        $o = new ComNotification_Model_Log();
        $o->site_id = CMS_Bazalt::getSiteId();

        return $o;
    }

    public static function getCollection()
    {
        $q = ORM::select('ComNotification_Model_Log l', 'l.*');
        return new CMS_ORM_Collection($q);
    }

    public static function getPlaned()
    {
        $limit = (int)CMS_Option::get(CMS_Mail::SMTP_MAX_MESSAGES_PER_MINUTE_OPTION, 290);
        $q = new ORM_Query('SELECT * FROM '.self::getCurrentTableName().' WHERE state = '.self::STATE_PLANED.' LIMIT '.$limit);
//        echo $q->toSQL();exit;
        $messages = $q->fetchAll('ComNotification_Model_Log');
//        print_r($messages);exit;
        return $messages;
    }

    public static function plan($address, $subject, $message)
    {
        $o = self::create();
        $o->address = $address;
        $o->subject = $subject;
        $o->message = $message;
        $o->state = self::STATE_PLANED;
        $o->created = gmdate('Y-m-d H:i:s');
        $o->save();
        return $o;
    }

    public static function updateState($id, $error, $state)
    {
        $query = 'UPDATE ' . self::getCurrentTableName().' SET state = ?';
        $params [] = $state;
        if($error) {
            $query .= ", error = ?";
            $params [] = $error;
        }
        $query .= ' WHERE id = '.$id;
        $q = new ORM_Query($query, $params);
        $q->exec(false);
    }


    public static function log($address, $subject, $message, $state, $error = null)
    {
        $o = self::create();
        $o->address = $address;
        $o->subject = $subject;
        $o->message = $message;
        $o->error = $error;
        $o->state = ($state == true) ? self::STATE_SEND_SUCCESS : self::STATE_SEND_FAILED;
        $o->created = gmdate('Y-m-d H:i:s');
        $o->save();
        return $o;
    }

    public static function archive()
    {
        $date = date('Y-m-d', strtotime('-1 week'));

        $sql = sprintf('INSERT INTO `%s` (`id`,`site_id`,`address`,`subject`,`message`,`error`,`state`,`created`)
        SELECT `id`,`site_id`,`address`,`subject`,`message`,`error`,`state`,`created` FROM `%s` WHERE DATE(`created`) <= \'%s\'', self::getArchiveTableName(), self::getCurrentTableName(), $date);
        $q = new ORM_Query($sql);
        $q->exec();


        $sql = sprintf('DELETE FROM `%s` WHERE DATE(`created`) <= \'%s\'', self::getCurrentTableName(), $date);
        $q = new ORM_Query($sql);
        $q->exec();
    }

    public function save()
    {
        if ($this->isPKEmpty()) {
            $connection = ORM_Connection_Manager::getConnection();
            $params = array();
            $query = 'INSERT INTO ' . self::getCurrentTableName();
            $query .= ' (';
            foreach ($this->getColumns() as $column) {
                $fieldName = $column->name();
                $query .= $connection->quote($fieldName) . ',';
            }
            $query = substr($query, 0, -1);
            $query .= ')';
            $query .= ' VALUES (';
            $queryVals = '';
            foreach ($this->getColumns() as $column) {
                $fieldName = $column->name();
                $queryVals .= '?,';
                $params [] = $this->{$fieldName};
            }
            $query .= substr($queryVals, 0, -1) . ')';
            $q = new ORM_Query($query, $params);
            $q->exec(false);
        } else {
            throw new Exception('not developed');
        }
    }

    public function delete()
    {
        throw new Exception('not developed');
    }

    public static function resendByDate($date)
    {
        $q = ORM::select('ComNotification_Model_Log l', 'l.*');
        $q->where('DATE(l.created) = ?', $date);
        $q->andWhere('l.state = ?', 0);
//        echo $q->toSQL();exit;
        $messages = $q->fetchAll('ComNotification_Model_Log');
        foreach($messages as $message) {
            $message->message = str_replace('Error: SMTP Error: Data not accepted.', '', $message->message);
            $res = call_user_func(array('CMS_Mail', 'send'), $message->address, $message->message, $message->subject);
            if($res) {
                $query = "UPDATE " . self::_getTableName(). " SET `state` = 1, `message` = '".$message->message."' WHERE `id` = ".$message->id;
//                echo $query;exit;
                $q = new ORM_Query($query, array());
                $q->exec(false);
            }
        }
    }
}