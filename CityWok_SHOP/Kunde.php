<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Kunde extends Page
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
        
        if (isset($_SESSION["mySession"])) {
            $session = $_SESSION["mySession"];
            $ids = explode(",", $session);
        } else {
            $ids[0] = 0;
        }
        /*
		if (isset($_COOKIE["myCookie"])) {
            $cookie = $_COOKIE["myCookie"];
            $ids = explode(",", $cookie);
        } else {
            $ids[0] = 0;
        }*/
		
        $firstid = $ids[0];
		$sql = "SELECT * FROM bestelltespeise WHERE fBId='$firstid'";
        if (sizeof($ids) > 1) {
            $or = "";
            for ($i=1; $i<sizeof($ids); $i++) {
                $nextid = $ids[$i];
                $or = $or." OR fBId='$nextid'";
            }
            $sql = $sql.$or;
        }
		$recordset = $this->_database->query ($sql);
		if (!$recordset) {
			throw new Exception("Abfrage fehlgeschlagen: ".$this->_database->error);
        }

		// read selected records into result array
		$record = $recordset->fetch_assoc();
		while ($record) {
            $sid = $record["SId"];
			$name = $record["fAName"];
			$status = $record["Status"];
            $bestellt["sid"] = $sid;
            $bestellt["name"] = $name;
            $bestellt["status"] = $status;
            $bestellung[] = $bestellt;
			$record = $recordset->fetch_assoc();
		}
        $recordset->free();

		return $bestellung;
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
        $bestellung = $this->getViewData();
        $this->generatePageHeader('City Wok - Kunde');
        
        // Body
        echo <<<HTML
        <h2> Kunde </h2>

            <div class="bestellung">

                <form  id="form4" accept-charset="UTF-8" >

                    <fieldset class="formular">
                        <legend> Bestellstatus </legend>
HTML;
        
        foreach($bestellung as $b){
            $name = htmlspecialchars($b["name"]);
            $status = htmlspecialchars($b["status"]);
            $id = htmlspecialchars($b["sid"]);

            $Status0 = "disabled";
            $Status1 = "disabled";
            $Status2 = "disabled";
            $Status3 = "disabled";

            switch($status){
                case 0: {
                    $Status0 = "checked";
                    break;
                }
                case 1: {
                    $Status1 = "checked";
                    break;
                }
                case 2: {
                    $Status2 = "checked";
                    break;
                }
                case 3: {
                    $Status3 = "checked";
                    break;
                }
            }
        
            echo <<<HTML
                        <fieldset class="formular">
                            <legend> $name </legend>
                            
                            <div class="radios">
                                <label>
                                    <span>Bestellt</span>
                                    <input type="radio" name="$id" value="0" $Status0 />
                                </label>

                                <label>
                                    <span>In Zubereitung</span>
                                    <input type="radio" name="$id" value="1"  $Status1 />
                                </label>

                                <label>
                                    <span>Fertig</span>
                                    <input type="radio" name="$id" value="2" $Status2 />
                                </label>

                                <label>
                                    <span>Unterwegs</span>
                                    <input type="radio" name="$id" value="3" $Status3 />
                                </label>
                            </div>
                        </fieldset>
HTML;
        }
        
        echo <<<HTML
                    </fieldset>
                </form>
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
        session_start();
        if (isset($_POST["Vorname"]) && isset($_POST["Nachname"]) && isset($_POST["Strasse"]) && isset($_POST["Hausnummer"]) 
        && isset($_POST["PLZ"]) && isset($_POST["Wohnort"]) && isset($_POST["Telefon"]) && isset($_POST["speisen"]) && isset($_POST["anzahl"])) {
            
            // Aufruf durch Formular
			$fVorname = $_POST["Vorname"];
            $fNachname = $_POST["Nachname"];
            $fStrasse = $_POST["Strasse"];
            $fHausnummer = $_POST["Hausnummer"];
            $fPlz = $_POST["PLZ"];
            $fWohnort = $_POST["Wohnort"];
            $fTelefon = $_POST["Telefon"];
            $fSpeisen = array();
            $fSpeisen = $_POST["speisen"];
            $fAnzahl = array();
            $fAnzahl = $_POST["anzahl"];

            if (strlen($fVorname)<=0 || strlen($fNachname)<=0 || strlen($fStrasse)<=0 || strlen($fHausnummer)<=0 || strlen($fPlz)<=0 || strlen($fWohnort)<=0 || strlen($fTelefon)<=0 || sizeof($fSpeisen)<=0 || sizeof($fAnzahl)<=0 ) {
				throw new Exception("Bitte geben Sie in allen Feldern etwas an!");
            } else {
                
                $sqlVorname =$this->_database->real_escape_string($fVorname);
                $sqlNachname = $this->_database->real_escape_string($fNachname);
                $sqlStrasse = $this->_database->real_escape_string($fStrasse);
                $sqlHausnummer = $this->_database->real_escape_string($fHausnummer);
                $sqlPlz = $this->_database->real_escape_string($fPlz);
                $sqlWohnort = $this->_database->real_escape_string($fWohnort);
                $sqlTelefon = $this->_database->real_escape_string($fTelefon);
                
            
                // also neu eintragen!
                $SQLabfrage = "INSERT INTO bestellung SET ".
                    "Adresse = \"$sqlVorname, $sqlNachname, $sqlStrasse, $sqlHausnummer, $sqlPlz, $sqlWohnort, $sqlTelefon\"";
                $this->_database->query ($SQLabfrage);
                
                $id = $this->_database->insert_id;
                
                if (isset($_SESSION["mySession"])) {
                    $session = $_SESSION["mySession"];
                    $newVal = "".$session.",".$id."";
                    $_SESSION["mySession"] = $newVal;
                } else {
                    $_SESSION["mySession"] = $id;
                }
                
                /*if (isset($_COOKIE["myCookie"])) {
                    $cookie = $_COOKIE["myCookie"];
                    $newVal = "".$cookie.",".$id."";
                    setcookie("myCookie", $newVal, 0);
                } else {
				    setcookie("myCookie", (string)$id, 0);
                }*/
                
                for($i=0; $i<sizeof($fSpeisen); $i++) {
                    for($k=0; $k<$fAnzahl[$i]; $k++) {
                        $sqlSpeisen= $this->_database->real_escape_string($fSpeisen[$i]);
                        $SQLabfrage1 = "INSERT INTO bestelltespeise SET ".
                            "fAName = \"$sqlSpeisen\", fBId = \"$id\", Status = \"0\"";
                        $this->_database->query ($SQLabfrage1);
                    }
                }
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
            $page = new Kunde();
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
Kunde::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >