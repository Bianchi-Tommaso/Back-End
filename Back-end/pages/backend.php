<?php

require ("backendDataBase.php");

    $data = array();
    
    $start = $_POST['start'];
    $size = $_POST['length'];
    $search = $_POST['search']['value'];
    

    $json_respond;

    $backend = new backendDataBase();

if($_SERVER['REQUEST_METHOD'] === 'POST')
if(isset($_POST['data']))
{

    $data['id'] = array_keys($_POST['data'])[0];
    $firstName = $_POST['data'][$data['id'] ]['first_name'];
    $lastName = $_POST['data'][$data['id'] ]['last_name'];
    $gender = $_POST['data'][$data['id'] ]['gender'];
    if($_POST['action'] == "create")
    {
        $json_respond = $backend->POST($firstName, $lastName, $gender);
        $json_respond = array();
    }
    else if($_POST['action'] == "edit")
    {
        
        if($_POST['data'][$data['id']]['removed_date'] != "")
        {
            $json_respond = $backend->DELETE($data['id']);
        }
        else
        {
            $json_respond = $backend->PUT($firstName, $lastName, $gender, $data['id']);
            $json_respond = array();
        }
    }
}
    else
    {
        $json_respond = $backend->GET($start, $size, $search, $firstName, $lastName, $gender);
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