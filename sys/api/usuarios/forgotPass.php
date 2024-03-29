<?php
include '../../conexao.php';

cantLog($__EMAIL__);

header('Content-Type: application/json; charset=utf-8');

$request    = file_get_contents('php://input');
$json       = json_decode($request);

$email = scapeString($__CONEXAO__, $json->email);
$email = setEmail($email);

if(stopUserExistnt($__CONEXAO__, $email)){
    endCode('Usuário não existe', false);
}

$_query_ = mysqli_query($__CONEXAO__, "select * from users where email='$email'");
$__query__ = mysqli_fetch_assoc($_query_);
$codeDate = $__query__['codeDate'];
$codigo = $__query__['verifycode'];
if($__TIME__ - 600 < $codeDate){
    endCode(array("text" => "Já foi enviado um código para seu email recentemente", "code" => $codigo), true);
}

$subject = "Seu código de verificação para entrar no Voleibol Escolinha";
$message = "
    <html>
        <head>
            <title>$subject</title>
            <style>
                body { font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>
            <div style='background-color:#114494; color:white; text-align:center; padding: 5px; border-radius: 5px'>
                <h2 style='color:white;'>Código de verificação caso tenha esquecido sua senha</h2>
            </div>
            <div style='text-align:center; padding: 5px'>
                <p style='color:black;'>Código</p>
                <h1 style='color:black;'>$__CODE__</h1>
                <p style='color:black; font-size: 13px'>Obrigado por contar conosco!</p>
                <p style='color:black; font-size: 12px'>Todos os direitos reservados - Easycodex $__YEAR__</p>
            </div>
        </body>
    </html>
";

$codigo = encrypt($__CODE__);

$sendEmail = mail(decrypt($email), $subject, $message, implode("\r\n", $__HEADERS__));
mysqli_query($__CONEXAO__, "update users set verifycode='$codigo', codeDate='$__TIME__' where email='$email'");
endCode(array("text" => "Um código de verificação foi enviado para seu email", "code" => $codigo), true);