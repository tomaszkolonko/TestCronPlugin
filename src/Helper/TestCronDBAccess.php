<?php

namespace iLUB\Plugins\TestCron\Helper;


use ilDB;
use ilTestCronPlugin;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class TestCronDBAccess {
    /**
     * @var ilDB
     */
    protected $db;

    /**
     * @var $this->logger
     */
    protected $logger;

    use DIC;

    /**
     *
     * @throws
     */
    public function __construct() {
        $this->logger = new Logger("TestCronDBAccess");
        $this->logger->pushHandler(new StreamHandler(ilTestCronPlugin::LOG_DESTINATION), Logger::DEBUG);
        $this->logger->info("inside the constructor");
        $this->db = $this->dic()->database();

    }

    /**
     * @inheritdoc
     */
    public function allAnonymousUsers() {
        $this->logger->info("access all anonymous users... \n");
        $sql = "SELECT * FROM usr_session WHERE user_id = 13";
        $query = $this->db->query($sql);

        while ($rec = $this->db->fetchAssoc($query)) {
            $msg = 'id: ' . $rec['user_id'] . ' valid till: ' . date('Y-m-d - H:i:s', $rec['ctime']) . "\n";
            $this->logger->info($msg);
        }

        return ($this->db->numRows($query) > 0);
    }

    /**
     * @return mixed
     */
    public function getExpirationValue() {
        $sql = "SELECT expiration FROM tcron_config";
        $query = $this->db->query($sql);
        $rec = $this->db->fetchAssoc($query);

        return $rec['expiration'];
    }

    /**
     * Updates an entry determined by id with new information
     *
     * @param bool $as_obj
     */
    public function updateExpirationValue($expiration) {
        $this->db->manipulate('UPDATE ' . ilTestCronPlugin::TABLE_NAME . ' SET' .
            ' expiration = ' . $this->db->quote($expiration, 'integer') . ';'
        );
    }

    /**
     * Removes the table from DB after uninstall is triggered.
     */
    public function removePluginTableFromDB() {
        $sql = "DROP TABLE " . ilTestCronPlugin::TABLE_NAME;
        $this->db->query($sql);
    }
}