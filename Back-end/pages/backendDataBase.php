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

    public function POST($start, $size, $search)
    {
        $this->start = $start;
        $this->size = $size;
        $this->connessione = $this->accesso->OpenCon();
        $this->search = $search;

        if($this->search != null)
        {
            $queryGet = "SELECT * FROM employees WHERE first_name LIKE '$search' LIMIT " .$start .", ".$size;
        }
        else
        {
            $queryGet = "SELECT * FROM employees LIMIT " .$start .", ".$size;
        }

        $queryGet = "SELECT * FROM employees LIMIT " .$start .", ".$size;

        $risultato = $this->JSON($this->connessione->query($queryGet));

        return $risultato;

    }

    /*
    public function POST($data)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryPost = "INSERT INTO employees VALUES(DEFAULT, '$data->birthDate', '$data->firstName', '$data->lastName', '$data->gender', '$data->hireDate');";
        $this->connessione->query($queryPost);

        $this->accesso->CloseCon($this->connessione);
    }
    */

    public function PUT($data)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryPut = "UPDATE employees SET birth_date = '$data->birthDate', first_name = '$data->firstName', last_name = '$data->lastName', gender = '$data->gender', hire_date = '$data->hireDate' WHERE id = '$data->id';";
        $this->connessione->query($queryPut);

        $this->accesso->CloseCon($this->connessione); 
    }

    public function DELETE($data)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryDelete = "DELETE FROM employees WHERE id = '$data->id';";
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

                    'id' => $righe["id"], 
                    'birth_date' => $righe["birth_date"], 
                    'first_name' => $righe["first_name"], 
                    'last_name' => $righe["last_name"], 
                    'gender' => $righe["gender"], 
                    'hire_date' => $righe["hire_date"]));   
            }

            $conta = $this->ContaPagine();
            $paginaTotale = intval($conta/ $this->size);

            $json['recordsFiltered'] = $paginaTotale;
            $json['recordsTotal'] = $conta;
            
            
        }
        return $json;
    }
}

?>