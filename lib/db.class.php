<?php 
class DB {
  private $host;
  private $user;
  private $password;
  private $database;
  private $con;

  function __construct($host, $user, $password, $database) {
    $this->host = $host;
    $this->user = $user;
    $this->password = $password;
    $this->database = $database;
    $this->connect();
  }

  private function connect() {
    $this->con = new mysqli($this->host, $this->user, $this->password, $this->database);

    if ($this->con->connect_errno) {
      die($this->con->connect_error);
    }
  }

  function getData($query, $params = null) {
    
    if (!($stmt = $this->con->prepare($query)))  {
      exit('Failed to prepare query:' . $this->con->error);
    } 

    if ($params && count($params) != 0) {
      $typeString = '';
      
      foreach ($params as $p) {
        $typeString = $typeString . 's';
      }

      if (!$stmt->bind_param($typeString, ...$params)) {
        exit('Failed to bind params: ' . $stmt->error);
      };
    }

    if (!$stmt->execute()) {
      exit('Failed to execute query.' . $stmt->error);
    };
    
    $result = $stmt->get_result();

    if (mysqli_num_rows($result)) {
      $rows = [];

      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      };

      return $rows;
    } else {
      return 0;
    }

    $stmt->close();
  }

  function alterData($query, $params = null, $getError = false) {
    if ($getError) {

      if (!($stmt = $this->con->prepare($query)))  {
        return $this->con->error;
      }
      
      if ($params && count($params) !== 0) {
        $typeString = '';
        
        foreach ($params as $p) {
          $typeString = $typeString . 's';
        }
  
        if (!$stmt->bind_param($typeString, ...$params)) {
          return $stmt->error;
        };
      }
  
      if (!$stmt->execute()) {
        return $stmt->error;
      };

    } else {
      
      if (!($stmt = $this->con->prepare($query)))  {
        exit('Failed to prepare query.' . $this->con->error);
      }
      
      if ($params && count($params) !== 0) {
        $typeString = '';
        
        foreach ($params as $p) {
          $typeString = $typeString . 's';
        }
  
        if (!$stmt->bind_param($typeString, ...$params)) {
          exit('Failed to bind params.' . $stmt->error);
        };
      }
  
      if (!$stmt->execute()) {
        exit('Failed to execute query.' . $stmt->error);
      };

    }


    $stmt->close();
  }

  function getConnection() {
    return $this->con;
  }

}
?>