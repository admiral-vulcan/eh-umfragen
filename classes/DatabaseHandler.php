<?php
/**
 * Class DatabaseHandler
 *
 * Provides basic functionality for connecting to the database and handling database operations.
 * This class should be extended by other classes that perform specific database operations.
 */

namespace EHUmfragen;

use PDO;

class DatabaseHandler {
    /**
     * @var PDO The database connection
     */
    protected PDO $connection;

    /**
     * DatabaseHandler constructor.
     *
     * Initializes the database connection using the provided credentials.
     */
    public function __construct() {
        require_once ("gitignore/code.php");
        require_once ("sanitize.php");
        require_once("gitignore/dbcred.php");
        require_once ("passwordcheck.php");

        $dbuser = $GLOBALS["dbuser"];
        $dbpwd = $GLOBALS["dbpwd"];
        $this->connection = new PDO('mysql:host=localhost;dbname=eh-umfragen-2', $dbuser, $dbpwd);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
