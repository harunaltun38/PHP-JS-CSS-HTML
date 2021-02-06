<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';


class Index extends Page
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
     * @return none
     */
    protected function getViewData()
    {
        $speisen = array();
		$sql = "SELECT * FROM angebot";
		$recordset = $this->_database->query ($sql);
		if (!$recordset) {
			throw new Exception("Abfrage fehlgeschlagen: ".$this->_database->error);
        }

		// read selected records into result array
		$record = $recordset->fetch_assoc();
		while ($record) {
			$name = $record["AName"];
			$preis = $record["APreis"];
            $img = $record["AImg"];
            $speise["name"] = $name;
            $speise["preis"] = $preis;
            $speise["img"] = $img;
			$speisen[] = $speise;
			$record = $recordset->fetch_assoc();
		}
		$recordset->free();
		return $speisen;
        // to do: fetch data for this view from the database
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
        $speisen = $this->getViewData();
        $this->generatePageHeader('City Wok - Bestellung');
        
        
        // Body
        echo <<<HTML
        <section class="speisekarte">
        <h2> Speisekarte </h2>
            
            <div class="speisen">
            
HTML;
        foreach($speisen as $s) {
            $id = str_replace(' ', '', $s["name"]);
            $name = htmlspecialchars($s["name"]);
            $preis = htmlspecialchars($s["preis"]);
            $img = htmlspecialchars($s["img"]);
            echo <<<HTML
                <section class="speise">
                    <img src="$img" alt="" title="$name" id="$id" data-price="$preis" class="speiseImg" onclick="addToCartClicked(this)" />
                    <h3>$name</h3>
                    <p>$$preis</p>
                </section>
HTML;
        }
        echo <<<HTML
            </div>
            </section>
            
            <section class="bestellung">
                <h2> Bestellung </h2>

                <form action="Kunde.php" id="form1" accept-charset="UTF-8" method="post">

                    <fieldset class="formular">
                        <legend> Bestelldaten </legend>

                        <fieldset class="adresse">
                            <legend> Adressdaten </legend>

                            <label class="a_item"> Vorname :
                                <input type="text" pattern="[A-Za-z]{2,}" class="input-field" name="Vorname" value="" size="20" maxlength="20" placeholder="Ihr Vorname" onchange="checkValidation()" required />
                                <span class="tooltip">Der Vorname darf nur aus Buchstaben bestehen und muss eine Länge zwischen 2 und 20 haben.</span>
                            </label>

                            <label class="a_item"> Nachname:
                                <input type="text" pattern="[A-Za-z]{2,}" class="input-field" name="Nachname" value="" size="20" maxlength="20" placeholder="Ihr Nachname" onchange="checkValidation()" required />
                                <span class="tooltip">Der Nachname darf nur aus Buchstaben bestehen und muss eine Länge zwischen 2 und 20 haben.</span>
                            </label>

                            <label class="a_item"> Straße:
                                <input type="text" pattern="[A-Za-z]{3,}" class="input-field" name="Strasse" value="" size="20" maxlength="20" placeholder="Ihre Straße" onchange="checkValidation()" required />
                                <span class="tooltip">Die Straße darf nur aus Buchstaben bestehen und muss eine Länge zwischen 3 und 20 haben.</span>
                            </label>

                            <label class="a_item"> Hausnumer:
                                <input type="text" class="input-field" name="Hausnummer" value="" size="20" maxlength="5" placeholder="Ihre Hausnummer" onchange="checkValidation()" required />
                                <span class="tooltip">Die Hausnummer darf maximal 5 Zeichen lang sein.</span>
                            </label>

                            <label class="a_item"> Postleitzahl:
                                <input pattern="[0-9]{5}" class="input-field" title="Fünfstellige PLZ in DE" type="text" name="PLZ" value="" size="5" maxlength="5" placeholder="Ihre PLZ" onchange="checkValidation()" required />
                                <span class="tooltip">Die PLZ darf nur aus Ziffern von 0-9 bestehen und muss die Länge 5 haben.</span>
                            </label>

                            <label class="a_item"> Wohnort:
                                <input type="text" pattern="[A-Za-z]{3,}" class="input-field" name="Wohnort" value="" size="20" maxlength="20" placeholder="Ihr Wohnort" onchange="checkValidation()" required />
                                <span class="tooltip">Der Wohnort darf nur aus Buchstaben bestehen und muss eine Länge zwischen 3 und 20 haben.</span>
                            </label>
                            
                            <label class="a_item"> Telefonnummer:
                                <input type="tel" pattern="[0-9]{5,}" class="input-field" name="Telefon" value="" size="20" maxlength="15" placeholder="Ihre Telefonnummer" onchange="checkValidation()" required />
                                <span class="tooltip">Die Telefonnummer darf nur aus Ziffern von 0-9 bestehen und muss eine Länge zwischen 5 und 15 haben.</span>
                            </label>
                            
                        </fieldset>
                        
                        <div class="container content-section">
                            <h3 class="section-header">Warenkorb</h3>
                            <div class="cart-row">
                                <span class="cart-item cart-header cart-column">SPEISE</span>
                                <span class="cart-price cart-header cart-column">PREIS</span>
                                <span class="cart-quantity cart-header cart-column">ANZAHL</span>
                            </div>
                            <div class="cart-items">
                            </div>
                            <div class="cart-total">
                                <span class="cart-total-title">Total</span>
                                <span class="cart-total-price">$0</span>
                            </div>
                            <input class="btn btn-primary btn-purchase" type="submit" value="BESTELLEN" onclick=purchaseClicked()/>
                        </div>

                    </fieldset>

                </form>
            </section>
            <script src="warenkorb.js" async> </script>
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
            $page = new Index();
            $page->processReceivedData();  //Anfragen bearbeiten und DB Informationen
            $page->generateView();     //Ausgabe an den Client  ->(HTML)
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Index::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >