<?php

require("../accessoDB/accessoDB.php");

class backendDataBase
{
    private $start;
    private $size;
    private $search;
    private $connessione;
    private $accesso; 

     function __construct()
    {
        $this->accesso = new accessoDB();
    }

    public function GET($start, $size, $search, $firstName, $lastName, $gender)
    {
        $this->start = $start;
        $this->size = $size;
        $this->connessione = $this->accesso->OpenCon();
        $this->search = $search;

        if($search=="")
        {
            $queryGet = "SELECT * FROM employees LIMIT " .$start .", ".$size;
            
        }
        else
        {
            $queryGet = "SELECT * FROM employees WHERE first_name LIKE '%".$search."%' OR last_name LIKE '%".$search."%'  LIMIT " . $start .", ". $size;
        }

        $risultato = $this->JSON($this->connessione->query($queryGet));

        return $risultato;

    }

    
    public function POST($firstName, $lastName, $gender)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryPost = "INSERT INTO employees VALUES (DEFAULT, '2003-12-26',  '$firstName', '$lastName', '$gender', '2003-12-26');";
        $this->connessione->query($queryPost);

        $this->accesso->CloseCon($this->connessione);
    }


    public function PUT($firstName, $lastName, $gender, $id)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryPut = "UPDATE employees SET first_name = '$firstName', last_name = '$lastName', gender = '$gender' WHERE id = '$id';";
        $this->connessione->query($queryPut);

        $this->accesso->CloseCon($this->connessione); 
    }

    public function DELETE($id)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryDelete = "DELETE FROM employees WHERE id = '$id';";
        $this->connessione->query($queryDelete);

        $this->accesso->CloseCon($this->connessione); 
    }

    public function ContaPagine()
    {
        $tot = 0;
        $contaQuery = "SELECT COUNT(id) FROM employees";

        $risultato = $this->connessione->query($contaQuery);

        $this->accesso->CloseCon($this->connessione);

        for(;$righe = $risultato->fetch_assoc();)
        {
            $tot=$righe['COUNT(id)'];
        }
        return $tot;
    }

    public function JSON($risultato)
    {
        $json = array();
    
        if($risultato->num_rows > 0)
        {
            $json['data'] = array();
            


            for(;$righe = $risultato->fetch_assoc();)
            {
                array_push($json['data'], array(

                    'DT_RowId' => $righe["id"], 
                    'birth_date' => $righe["birth_date"], 
                    'first_name' => $righe["first_name"], 
                    'last_name' => $righe["last_name"], 
                    'gender' => $righe["gender"], 
                    'hire_date' => $righe["hire_date"]));   
            }

            $conta = $this->ContaPagine();
            $paginaTotale = intval($conta/ $this->size);

            $json['recordsFiltered'] = $conta;
            $json['recordsTotal'] = $conta;
            
            
        }
        return $json;
    }
}

?>