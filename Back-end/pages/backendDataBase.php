<?php

require("../accessoDB/accessoDB.php");

class backendDataBase
{
    private $page;
    private $size;
    private $connessione;
    private $accesso; 

     function __construct()
    {
        $this->accesso = new accessoDB();
    }

    public function GET($page, $size)
    {
        $this->page = $page;
        $this->size = $size;
        $this->connessione = $this->accesso->OpenCon();

        $queryGet = "SELECT * FROM employees LIMIT " .$page * $size.", ".$size;

        $risultato = $this->JSON($this->connessione->query($queryGet));        

        return $risultato;

    }

    public function POST($data)
    {
        $this->connessione = $this->accesso->OpenCon();

        $queryPost = "INSERT INTO employees VALUES(DEFAULT, '$data->birthDate', '$data->firstName', '$data->lastName', '$data->gender', '$data->hireDate');";
        $this->connessione->query($queryPost);

        $this->accesso->CloseCon($this->connessione);
    }

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
            $righe = $risultato->fetch_assoc();

            $json['_embedded'] = array();
            $json['_embedded']['_employees'] = array();

            for(;$righe = $risultato->fetch_assoc();)
            {
                $oggetto = array(array('id' => $righe["id"], 'birthDate' => $righe["birth_date"], 'firstName' => $righe["first_name"], 'lastName' => $righe["last_name"], 'gender' => $righe["gender"], 'hireDate' => $righe["hire_date"]),
                    array('_links' => array('self' =>array('href'=>'http://localhost:8080/pages/backend.php/' . $righe['id']),
                        'employees' =>array('href'=>'http://localhost:8080/pages/backend.php/' . $righe['id']))));
                    array_push($json['_embedded']['_employees'], $oggetto);
            }

            $pagina = $this->page;
            $next = $this->page + 1;
            $conta = $this->ContaPagine();
            $paginaTotale = intval($conta/ 20);
            $json['_links']['self'] = array('href' => 'http://localhost:8080/pages/backend.php{?page,size,sort}');
            $json['_links']['first'] = array('href' => 'http://localhost:8080/pages/backend.php?page=0&size=20');
            $json['_links']['next'] = array('href' => 'http://localhost:8080/pages/backend.php?page='. $next  .'&size=20');
            $json['_links']['last'] = array('href' => 'http://localhost:8080/pages/backend.php?page='. $paginaTotale .'&size=20');
            $json['_links']['prev'] = array('href' => 'http://localhost:8080/pages/backend.php?page=' . ($pagina - 1) .'&size=20');
            $json['_links']['page'] = array('size' => 20, 'number' => intval($pagina), 'totalElements' => $conta, 'totalPages' => $paginaTotale);
            
        }
        return $json;
    }
}

?>