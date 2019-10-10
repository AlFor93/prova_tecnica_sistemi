<?php
class User{

    // database connection and table name
    private $conn;
    private $table_name = "users";

    // object properties
    public $user_id;
    public $firstname;
    public $lastname;
    public $email;
    public $user_password;
    public $customer_id;
    public $customer_name;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read users
  function read(){

    // select all query
    $query = "SELECT
                c.name as customer_name, u.user_id, u.firstname, u.lastname, u.email, u.password, u.customer_id, u.created
            FROM
                " . $this->table_name . " u
                LEFT JOIN
                    customers c
                        ON u.customer_id = c.id
            ORDER BY
                u.created DESC";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // execute query
    $stmt->execute();

    return $stmt;
  }

// create user
function create(){

    // query to insert record
    $query = "INSERT INTO
                " . $this->table_name . "
            SET
                firstname=:firstname, lastname=:lastname, email=:email, password=:password, customer_id=:customer_id, created=:created";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->firstname=htmlspecialchars(strip_tags($this->firstname));
    $this->lastname=htmlspecialchars(strip_tags($this->lastname));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->password=htmlspecialchars(strip_tags($this->password));
    $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
    $this->created=htmlspecialchars(strip_tags($this->created));

    // bind values
    $stmt->bindParam(":firstname", $this->firstname);
    $stmt->bindParam(":lastname", $this->lastname);
    $stmt->bindParam(":email", $this->email);
    // $stmt->bindParam(":password", $this->password);
    // hash the password before saving to database
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(":password", $password_hash);
    $stmt->bindParam(":customer_id", $this->customer_id);
    $stmt->bindParam(":created", $this->created);


    // execute query
    if($stmt->execute()){
        return true;
    }

    return false;

}

// used when filling up the update user form
function readOne(){

    // query to read single record
    $query = "SELECT
                c.name as customer_name, u.user_id, u.firstname, u.lastname, u.email, u.password, u.customer_id, u.created
            FROM
                " . $this->table_name . " u
                LEFT JOIN
                    categories c
                        ON u.customer_id = c.id
            WHERE
                u.user_id = ?
            LIMIT
                0,1";

    // prepare query statement
    $stmt = $this->conn->prepare( $query );

    // bind id of user to be updated
    $stmt->bindParam(1, $this->user_id);

    // execute query
    $stmt->execute();

    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // set values to object properties
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->email = $row['email'];
    // $this->price = $row['price'];
    $this->customer_id = $row['customer_id'];
    $this->customer_name = $row['customer_name'];
}



}
