<?php
include '../../conexao.php';

justLog($__EMAIL__, $__TYPE__, 1);

header('Content-Type: application/json; charset=utf-8');

$request = file_get_contents('php://input');
$json = json_decode($request);

$pass   = scapeString($__CONEXAO__, $json->pass);
$email  = scapeString($__CONEXAO__, $json->email);

if(($pass == "" or $pass == " ") and encrypt($email) == $__EMAIL__){
    endCode("Nenhum dado válido", false);
}

if($email == "" or $email == " "){
    endCode("Email inválido", false);
}

$dados = "";

if($pass and $pass != " "){
    $pass   =  password_hash($pass, PASSWORD_DEFAULT);
    $dados  .= "(senha)";
    $_SESSION["password"] = $pass;
    mysqli_query($__CONEXAO__, "update users set senha='$pass' where id='$__ID__'");
}

if($email and $email != "" and $email != " "){
    $email  = setEmail($email);
    if($email != $__EMAIL__){
        stopUserExist($__CONEXAO__, $email, 0);
        $dados  .= "(email)";
        $_SESSION["email"] = $email;
        mysqli_query($__CONEXAO__, "update users set email='$email' where id='$__ID__'");
    }
}

endCode("Dados atualizados: $dados", true);