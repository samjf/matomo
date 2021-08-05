<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\DataAccess;

use Piwik\Config;
use Piwik\Db\AdapterInterface;
use Piwik\DbHelper;
use Psr\Log\LoggerInterface;

class ArchivingDbAdapter
{
    /**
     * @var AdapterInterface|\Zend_Db_Adapter_Abstract
     */
    private $wrapped;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $maxExecutionTime;

    public function __construct($wrapped, LoggerInterface $logger = null)
    {
        $this->wrapped = $wrapped;
        $this->logger = $logger;
        $this->maxExecutionTime = (float) Config::getInstance()->General['archiving_query_max_execution_time'];
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->wrapped, $name], $arguments);
    }

    public function exec($sql)
    {
        $arguments = func_get_args();
        $arguments[0] = DbHelper::addMaxExecutionTimeHintToQuery($arguments[0], $this->maxExecutionTime);
        $this->logSql($arguments[0]);

        return call_user_func_array([$this->wrapped, __FUNCTION__], $arguments);
    }

    public function query($sql)
    {
        $arguments = func_get_args();
        $arguments[0] = DbHelper::addMaxExecutionTimeHintToQuery($arguments[0], $this->maxExecutionTime);
        $this->logSql($arguments[0]);

        return call_user_func_array([$this->wrapped, __FUNCTION__], $arguments);
    }

    public function fetchAll($sql)
    {
        $arguments = func_get_args();
        $arguments[0] = DbHelper::addMaxExecutionTimeHintToQuery($arguments[0], $this->maxExecutionTime);
        $this->logSql($arguments[0]);

        return call_user_func_array([$this->wrapped, __FUNCTION__], $arguments);
    }

    public function fetchRow($sql)
    {
        $arguments = func_get_args();
        $arguments[0] = DbHelper::addMaxExecutionTimeHintToQuery($arguments[0], $this->maxExecutionTime);
        $this->logSql($arguments[0]);

        return call_user_func_array([$this->wrapped, __FUNCTION__], $arguments);
    }

    public function fetchOne($sql)
    {
        $arguments = func_get_args();
        $arguments[0] = DbHelper::addMaxExecutionTimeHintToQuery($arguments[0], $this->maxExecutionTime);
        $this->logSql($arguments[0]);

        return call_user_func_array([$this->wrapped, __FUNCTION__], $arguments);
    }

    public function fetchAssoc($sql)
    {
        $arguments = func_get_args();
        $arguments[0] = DbHelper::addMaxExecutionTimeHintToQuery($arguments[0], $this->maxExecutionTime);
        $this->logSql($arguments[0]);

        return call_user_func_array([$this->wrapped, __FUNCTION__], $arguments);
    }

    private function logSql($sql)
    {
        // Log on DEBUG level all SQL archiving queries
        if ($this->logger) {
            $this->logger->debug($sql);
        }
    }
}