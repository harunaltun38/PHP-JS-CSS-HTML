<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';


class Fahrer extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    
    /**
     * Instantiates members (to be defined above).   
     * Calls the constructor of the parent i.e. page class.
     * So the database connection is established.
     *
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
        $bestellungen = array();
        $data = array();
        
        if (isset($_COOKIE["myCookie"])) {
            setcookie("Admin", "Superuser", 1);
            throw new Exception("Zugriff nur für Mitarbeiter");
        } else {
            setcookie("Admin", "Superuser", 0);
        }

        $sqlA = "SELECT BId, Adresse, Bestellzeitpunkt, SUM(APreis) AS bestellpreis FROM bestellung b JOIN bestelltespeise s ON b.BId = s.fBId JOIN angebot a ON s.fAName = a.AName GROUP BY BId HAVING SUM(Status < 2) = 0";
        $recordset1 = $this->_database->query ($sqlA);
        if (!$recordset1) {
			throw new Exception("Abfrage fehlgeschlagen: ".$this->_database->error);
        }

        $record1 = $recordset1->fetch_assoc();
		while ($record1) {
            $adresse = $record1["Adresse"];
            $bestellid = $record1["BId"];
            $zeitpunkt = $record1["Bestellzeitpunkt"];
            $bestellpreis = $record1["bestellpreis"];
            $data["bid"] = $bestellid;
            $data["adresse"] = $adresse;
            $data["zeitpunkt"] = $zeitpunkt;
            $data["bestellpreis"] = $bestellpreis;
            $data["speisen"] = array();
            
            $sqlb = "SELECT * FROM bestelltespeise WHERE fBId ='$bestellid' ORDER BY fAName";
            $recordset2 = $this->_database->query ($sqlb);
            if (!$recordset2) {
                throw new Exception("Abfrage fehlgeschlagen: ".$this->_database->error);
            }
            
            $record2 = $recordset2->fetch_assoc();
            while($record2){
                $sid = $record2["SId"];
                $name = $record2["fAName"];
                $status = $record2["Status"];
                
                $speisen = array();
                $speisen["sid"] = $sid;
                $speisen["name"] = $name;
                $speisen["status"] = $status;
                
                $data["speisen"][] = $speisen;
                $record2 = $recordset2->fetch_assoc();
            }
            $recordset2->free();
            $bestellungen[] = $data;
			$record1 = $recordset1->fetch_assoc();
		}
        $recordset1->free();
        
        return $bestellungen;
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
        $bestellungen = $this->getViewData();
        $this->generatePageHeader('City Wok - Fahrer');
        
        //print_r($adressen);

        // Body
        echo <<<HTML
		<meta http-equiv="refresh" content="5">
        <h2> Fahrer </h2>

            <div class="bestellung">

                <fieldset class="formular">
                    <legend> Bestellstatus </legend>
HTML;
        foreach($bestellungen as $b) {
            $adresse = htmlspecialchars($b["adresse"]);
            $bid = htmlspecialchars($b["bid"]);
            $zeitpunkt = htmlspecialchars($b["zeitpunkt"]);
            $preis = htmlspecialchars($b["bestellpreis"]);
            $speisen = array();
            $speisen = $b["speisen"];
            
            echo <<<HTML
            
                    <form action="Fahrer.php" id="form$bid" accept-charset="UTF-8" method="post">
                        <fieldset class="formular">
                            <legend> Bestellung $bid </legend>
                            <p> Kundenaddresse: $adresse </p>
                            <p> Auflistung der Bestellung:
HTML;
            $status = -1;
            $lastname = "";
            $anz = 0;
            foreach($speisen as $s) {
                $name = htmlspecialchars($s["name"]);
                $einzelstatus = htmlspecialchars($s["status"]);
                if ($status == -1) {
                    $status = $einzelstatus;
                    $lastname = $name;
                } elseif ($einzelstatus != $status) {
                    throw new Exception("Status der Bestellung nicht einheitlich.");
                } 
                if ($lastname == $name) {
                    $anz++;
                } else {
                    echo <<<HTML
                                $anz x $lastname,
HTML;
                    $lastname = $name;
                    $anz = 1;
                }
                if ($s == end($speisen)) {
                    echo <<<HTML
                                $anz x $name
HTML;
                }
            }
            $Status2 = "";
            $Status3 = "";
            $Status4 = "";

            switch($status){
                case 2: {
                    $Status2 = "checked";
                    break;
                }
                case 3: {
                    $Status3 = "checked";
                    break;
                }
                case 4: {
                    $Status4 = "checked";
                    break;
                }
            }
            echo <<<HTML
                            </p>
                            <p> Preis der Bestellung: $$preis </p>

                            <div class="radios">
                                <label>
                                    <span>Fertig</span>
                                    <input type="radio" name="status$bid" value="2" onclick="document.forms['form$bid'].submit();" $Status2 />
                                    <input type="hidden" id="$bid" name="myid" value="$bid" />
                                </label>

                                <label>
                                    <span>Unterwegs</span>
                                    <input type="radio" name="status$bid" value="3" onclick="document.forms['form$bid'].submit();" $Status3 />
                                </label>

                                <label>
                                    <span>Ausgeliefert</span>
                                    <input type="radio" name="status$bid" value="4" onclick="document.forms['form$bid'].submit();" $Status4 />
                                </label>
                            </div>
                        </fieldset>
                    </form>
HTML;
        }
        
        echo <<<HTML
                </fieldset>
                    
            </div>
HTML;
        
        
        $this->generatePageFooter();
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
        // to do: call processReceivedData() for all members
        
        if (isset($_POST["myid"])) {
            
            $fId = $_POST["myid"];
            $sqlId =$this->_database->real_escape_string($fId);
            if (isset($_POST["status$sqlId"])) {
                $fStatus = $_POST["status$sqlId"];
                $sqlStatus =$this->_database->real_escape_string($fStatus);
                if ($sqlStatus == 4) {
                    $SQLabfrage = "DELETE FROM bestelltespeise WHERE (fBId='$sqlId') ";
                } else {
                    $SQLabfrage = "UPDATE bestelltespeise SET Status = $sqlStatus WHERE (fBId='$sqlId') ";
                }
                $this->_database->query ($SQLabfrage);
            } else {
                throw new Exception("Status nicht empfangen.");
            }
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
            $page = new Fahrer();
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
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >