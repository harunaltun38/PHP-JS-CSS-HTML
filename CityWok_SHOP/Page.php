<?php	// UTF-8 marker äöüÄÖÜß€

abstract class Page
{
    // --- ATTRIBUTES ---

    /**
     * Reference to the MySQLi-Database that is
     * accessed by all operations of the class.
     */
    protected $_database = null;
    
    // --- OPERATIONS ---
    
    /**
     * Connects to DB and stores 
     * the connection in member $_database.  
     * Needs name of DB, user, password.
     *
     * @return none
     */
    protected function __construct() 
    {
        // activate full error checking
		error_reporting (E_ALL);
        // open database
        require_once 'pwd.php'; // read account data
		$this->_database = new MySQLi($host, $user, $pwd, "citywok");
        // check connection to database
	    if (mysqli_connect_errno()) {
	        throw new Exception("Keine Verbindung zur Datenbank: ".mysqli_connect_error());
        }
		// set character encoding to UTF-8
		if (!$this->_database->set_charset("utf8")) {
		    throw new Exception("Fehler beim Laden des Zeichensatzes UTF-8: ".$this->_database->error);
        }
    }
    
    /**
     * Closes the DB connection and cleans up
     *
     * @return none
     */
    protected function __destruct()    
    {
        $this->_database->close();
    }
    
    /**
     * Generates the header section of the page.
     * i.e. starting from the content type up to the body-tag.
     * Takes care that all strings passed from outside
     * are converted to safe HTML by htmlspecialchars.
     *
     * @param $headline $headline is the text to be used as title of the page
     *
     * @return none
     */
    protected function generatePageHeader($headline = "") 
    {
        $headline = htmlspecialchars($headline);
        header("Content-type: text/html; charset=UTF-8");
        
        // including the individual headline
        
        echo <<<HTML
<!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
HTML;
        if ($headline == 'City Wok - Kunde') {
            echo <<<HTML
        <meta http-equiv="refresh" content="5" />
HTML;
        }
        echo <<<HTML
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
        <link href="CityWok.css" type="text/css" rel="stylesheet" media="screen" />
        <title>$headline</title>
    </head>
    <body id="main">        
        <header>
            <nav class="navbar">
                <span class="open-slide">
                    <a href="#" onclick="openSlideMenu()">
                        <svg width="30" height="30">
                            <path d="M0,5 30,5" stroke="#ec7f37" stroke-width="5" />
                            <path d="M0,14 30,14" stroke="#ec7f37" stroke-width="5" />
                            <path d="M0,23 30,23" stroke="#ec7f37" stroke-width="5" />
                        </svg>
                    </a>
                </span>

                <ul class="navbar-nav">
                    <li><a href="Index.php">Bestellung</a></li>
                    <li><a href="Kunde.php">Kunde</a></li>
                    <li><a href="kueche.html">Küche</a></li>
                    <li><a href="Fahrer.php">Fahrer</a></li>
                </ul>
            </nav>
        </header>

        <article class="content">
            <div id="side-menu" class="side-nav">
                <a href="#" class="btn-close" onclick="closeSlideMenu()">&times;</a>
                <a href="Index.php">Bestellung</a>
                <a href="Kunde.php">Kunde</a>
                <a href="kueche.html">Küche</a>
                <a href="Fahrer.php">Fahrer</a>
            </div>
            
            
            <div class="title">
                <img src="city-wok-guy-head.png" alt="" title="CityWok" class="logo" />
                <h1>CITY WOK</h1>
                <img src="city-wok-guy-head.png" alt="" title="CityWok" class="logo" />
            </div>
HTML;
    }

    /**
     * Outputs the end of the HTML-file i.e. /body etc.
     *
     * @return none
     */
    protected function generatePageFooter() 
    {
        echo <<<HTML
        </article>
        <script src="CityWok.js"> </script>
    </body>
</html>
HTML;
    }

    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If every page is supposed to do something with submitted
     * data do it here. E.g. checking the settings of PHP that
     * influence passing the parameters (e.g. magic_quotes).
     *
     * @return none
     */
    protected function processReceivedData() 
    {
        if (get_magic_quotes_gpc()) {
            throw new Exception
                ("Bitte schalten Sie magic_quotes_gpc in php.ini aus!");
        }
    }
} // end of class

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >