<?php
  /*includo il dabase che contiene i dati per connettersi al server:
  $servername, $username, $password e $dbname*/
  include "databaseinfo.php";


  class Persona {
    /*Si utilizza private cosicché si debba per forza passare per un metodo
    per modificare i parametri di un oggetto. In questo modo è possibile inserire
    dei controlli come ad esempio se una variabile possa essere solo un numero*/

    private $name;
    private $lastname;

    public function __construct($name, $lastname) {

      $this->setName($name);
      $this->setLastname($lastname);
    }

    /*###
    RESTUTISCE IL NOME CHIAMANDO "$oggetto->getname();"
    #####*/
    function getName() {

      return $this->name;
    }

    /*###
    IMPOSTA IL NOME CHIAMANDO "$oggetto->setname("nuovoNome");"
    #####*/
    function setName($name) {

      /*Condizione: il numero di caratteri deve essere superiore a tre
      altrimenti imposta valore nome a -1*/
      if (strlen($name) > 3) {

        $this->name = $name;
      } else {

        $this->name = -1;
      }
    }



    function getLastname() {

      return $this->lastname;
    }
    function setLastname($lastname) {

      $this->lastname = $lastname;
    }
  }

  /*extends=clona prende le stesse variabili e funzione della classe Persona*/
  class Ospite extends Persona {

    private $dateOfBirth;
    private $documentType;
    private $documentNumber;

    function __construct($name, $lastname, $dateOfBirth, $documentType, $documentNumber) {

      /*parent::__construct serve per richiamare le variabili della classe Persona
      devono comunque essere aggiunte come paramentro alla function __construct*/
      parent::__construct($name, $lastname);

      $this->setDateOfBirth($dateOfBirth);
      $this->setDocumentType($documentType);
      $this->setDocumentNumber($documentNumber);
    }

    /*##########SET&GET############*/

    /*Non c'è bisogno di set&get'tare $name e $lastname perché il metodo
    è già reso disponibile da extends*/
    function getDateOfBirth() {

      return $this->dateOfBirth;
    }
    function setDateOfBirth($dateOfBirth) {

      $this->dateOfBirth = $dateOfBirth;
    }

    function getDocumentType() {

      return $this->documentType;
    }
    function setDocumentType($documentType) {

      $this->documentType = $documentType;
    }

    function getDocumentNumber() {

      return $this->documentNumber;
    }
    function setDocumentNumber($documentNumber) {

      $this->documentNumber = $documentNumber;
    }

    /*##########END SET&GET############*/

    /* static mi serve per prendere il concetto generale di ospite,
    andare sul database e farmio restuire tutti gli ospiti in formato array.
    ##vedasi la chiacchera sotto al while.##
    In questo caso è solo un modo più elegante per non fare tutto fuori dalla classe ospiti
    senza static avrei dovuto creare prima l'oggetto con new Ospiti e poi usare quella variabile
    per richiamare getAllOspiti()

    $ospite = new Ospite("name","lastname" -e tutti gli altri parametri richiesti-)
    poi per richiamarla:
    $ospiti = $ospite->getAllOspiti()

    con static mi basta fare:
    $ospiti=Ospite::getAllOspiti();

    */
    public static function getAllOspiti($conn) {

      $sql = "
              SELECT *
              FROM ospiti
      ";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $ospiti = [];
        while($row = $result->fetch_assoc()) {
          $ospiti[] =
              new Ospite($row["name"],
                         $row["lastname"],
                         $row["date_of_birth"],
                         $row["document_type"],
                         $row["document_number"]);
        }
      }
      return $ospiti;
    }
  }


  class Pagante extends Persona {

    private $address;

    function __construct($name, $lastname, $address) {

      parent::__construct($name, $lastname);
      $this->setAddress($address);
    }

    function getAddress() {

      return $this->address;
    }
    function setAddress($address) {

      $this->address = $address;
    }


    public static function getAllPaganti($conn) {

      $sql = "
              SELECT *
              FROM paganti
      ";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $paganti = [];
        while($row = $result->fetch_assoc()) {
          $paganti[] =
          /*Solo questi tre perché Paganti è una classe clone di Persona
          che ha solo name e lastname. Paganti aggiunge solo address*/
              new Pagante($row["name"],
                         $row["lastname"],
                         $row["address"]);
        }
      }
      return $paganti;
    }

    public static function getEPaganti($conn){

      $sql= "
        SELECT *
        FROM paganti
        WHERE name LIKE 'E%'
      ";

      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $pagantiNameStartE = [];
        while($row = $result->fetch_assoc()) {
          $pagantiNameStartE[] =
                 new Pagante($row["name"],
                         $row["lastname"],
                         $row["address"]);
        }
      }
      return $pagantiNameStartE;


    }

  }



  /*##########CHIAMATA AL SERVER################*/

  /*Questa è la chiamata alla connessione al server,
  dato che l'sql e il suo ritorno si trovano dentro ad un metodo
  posso usare questa chiamata ogni volta che mi serve*/
  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_errno) {

    echo $conn->connect_error;
    return;
  }

  /*######END CHIAMATA AL SERVER################*/



  /*trattandosi di un richiamo ad una funzione statica, la chiamata è diversa
  non è più $ospiti = $ospite->getAllOspiti($conn) ma diventa
  simile al parent:: dell'extends
  $variabile = Oggetto::metodo(param)*/
  $ospiti = Ospite::getAllOspiti($conn);

  $paganti = Pagante::getAllPaganti($conn);

  $pagantiNameStartE = Pagante::getEPaganti($conn);

  $conn->close();




  /*#########MS 1#################*/
  // foreach ($paganti as $key) {
  //   var_dump($key); echo "<br>";
  // }

  /*#########MS 2#################*/

  foreach ($pagantiNameStartE as $key) {
    var_dump($key); echo "<br>";
  }

 ?>
