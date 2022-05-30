<?php

require ("backendDataBase.php");

    $start = $_POST['start'];
    $size = $_POST['length'];
    $search = $_POST['search']['value'];
    $firstName = $_POST['data'][0]['first_name'];
    $lastName = $_POST['data'][0]['last_name'];
    $gender = $_POST['data'][0]['gender'];

    $json_respond;

    $data = readPostData();
    $backend = new backendDataBase();

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['action']))
    {
        $json_respond = $backend->POST($firstName, $lastName, $gender);
        $json_respond = array();
    }
    else
    {
        $json_respond = $backend->GET($start, $size, $search, $firstName, $lastName, $gender);
    }
}
else if($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $json_respond = $backend->GET($start, $size);
}
else if($_SERVER['REQUEST_METHOD'] === 'PUT')
{
    $json_respond = $backend->PUT($data);
}
else if($_SERVER['REQUEST_METHOD'] === 'DELETE')
{
    $json_respond = $backend->DELETE($data);
}

    header('Content-Type: application/json');      
    
    echo json_encode($json_respond, JSON_UNESCAPED_SLASHES);

function readPostData() 
{
    $json = file_get_contents('php://input');

    $data = json_decode($json);

    return $data;
}

?>