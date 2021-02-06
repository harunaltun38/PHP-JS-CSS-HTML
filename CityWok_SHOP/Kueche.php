<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';


class StatusItem {
    public $pizza_id;
    public $pizzaname;
    public $status;
}

class Kueche extends Page
{
    
    /**    
     * @return none
     */
    protected function __construct() 
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }
    
    /**
     * Cleans up what ever is needed.   
     * Calls the destructor of the parent i.e. page class.
     * So the database connection is closed.
     *
     * @return none
     */
    protected function __destruct() 
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return array
     */
    protected function getViewData()
    {
        // to do: fetch data for this view from the database
        $bestellung = array();
		
		if (isset($_COOKIE["myCookie"])) {
            setcookie("Admin", "Superuser", 1);
            throw new Exception("Zugriff nur für Mitarbeiter");
        } else {
            setcookie("Admin", "Superuser", 0);
        }
		
		$sql = "SELECT * FROM bestelltespeise 
        WHERE Status < 3";
		$recordset = $this->_database->query ($sql);
		if (!$recordset) {
			throw new Exception("Abfrage fehlgeschlagen: ".$this->_database->error);
        }

		// read selected records into result array
		$record = $recordset->fetch_assoc();
		while ($record) {
			$name = $record["fAName"];
            $status = $record["Status"];
            $Id = $record["SId"];
            /*
            $bestellt["name"] = $name;
            $bestellt["status"] = $status;
            $bestellt["id"] = $Id;*/
            $bestellt = new StatusItem();
            $bestellt->pizza_id = $Id;
            $bestellt->pizzaname = $name;
            $bestellt->status = $status;
            $bestellung[] = $bestellt;
			$record = $recordset->fetch_assoc();
		}
        $recordset->free();
        
        $json_data = json_encode($bestellung);

		return $json_data;

    }
    
    /**
     * First the necessary data is fetched and then the HTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView() 
    {
        $json_data = $this->getViewData();
        header("Content-type: application/json; charset=UTF-8");
        echo $json_data;
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this page is supposed to do something with submitted
     * data do it here. 
     * If the page contains blocks, delegate processing of the 
	 * respective subsets of data to them.
     *
     * @return none 
     */
    protected function processReceivedData() 
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all 
        if (isset($_POST["change"])) {
            $json = $_POST["change"];
            $change = json_decode($json, true);
            $sqlId = $this->_database->real_escape_string($change["pizza_id"]);
            $sqlStatus = $this->_database->real_escape_string($change["status"]);
            
            $SQLabfrage = "UPDATE bestelltespeise SET Status = $sqlStatus WHERE (SId='$sqlId') ";
            $this->_database->query ($SQLabfrage);
        }

    }

    /**
     * This main-function has the only purpose to create an instance 
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     *
     * @return none 
     */    
    public static function main() 
    {
        try {
            $page = new Kueche();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Kueche::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >