<?php
include '../../conexao.php';

cantLog($__EMAIL__);

header('Content-Type: application/json; charset=utf-8');

$request = file_get_contents('php://input');
$json = json_decode($request);

$email      = scapeString($__CONEXAO__, $json->email);
$password   = scapeString($__CONEXAO__, $json->password);

$checkEmail = setEmail($email);

checkMissing(array($checkEmail));

if(!$checkEmail){
    endCode("Email inválido", false);
}

$tryConnect = mysqli_query($__CONEXAO__, "select * from users where email='$checkEmail'");

if(mysqli_num_rows($tryConnect) < 1){
    endCode("Usuário não encontrado", false);
}

$passUser   = mysqli_fetch_assoc($tryConnect)["senha"];

$passwordV  = password_verify($password, $passUser);

if(!$passwordV){
    endCode("Senha incorreta", false);
}

mysqli_query($__CONEXAO__, "update users set lastLog='$__TIME__' where email='$checkEmail'");

$_SESSION['email'] = $checkEmail;
$_SESSION['password'] = $passUser;

endCode("Sucesso!", true);