<?php
include '../../conexao.php';

justLog($__EMAIL__, $__TYPE__, 2);

header('Content-Type: application/json; charset=utf-8');

$request = file_get_contents('php://input');
$json = json_decode($request);


$id             = scapeString($__CONEXAO__, $json->id);
$cpf            = scapeString($__CONEXAO__, $json->cpf);
$nome           = scapeString($__CONEXAO__, $json->nome);
$email          = scapeString($__CONEXAO__, $json->email);
$nascimento     = scapeString($__CONEXAO__, $json->nascimento);
$turmas         = $json->turmas;
$active         = scapeString($__CONEXAO__, $json->active);

$id             = setNum($id);
$cpf            = setCpf($cpf);
$nome           = setString($nome);
$email          = setEmail($email);
$nascimento     = setNum($nascimento);
$active         = setNum($active);

checkMissing(
    array(
        $id,
        $cpf,
        $nome,
        $email,
        $nascimento,
    )
);

$id = decrypt($id);
$active = decrypt($active);

$check = mysqli_query($__CONEXAO__, "select email from users where id='$id' and typeC='1'");

if(mysqli_num_rows($check) < 1){
    endCode("Aluno não existe", false);
}

$emm = mysqli_fetch_assoc($check);
$emm = $emm["email"];

if($__TYPE__ == 2){
    $checkAluno = mysqli_query($__CONEXAO__, "select id from alunos where turma in (select turma from professores where email='$__EMAIL__') and email in (select email from users where typeC='1' and id='$id')") or die("b");
    if(mysqli_num_rows($checkAluno) > 0){
        endCode("Esse aluno não pertence a você", false);
    }
}

$checkRepeat = mysqli_query($__CONEXAO__, "select id from users where email='$email' and id!='$id'");

if(mysqli_num_rows($checkRepeat) > 0){
    endCode("Email já está em uso.", false);
}

$checkRepeat = mysqli_query($__CONEXAO__, "select id from users where cpf='$cpf' and id!='$id'");

if(mysqli_num_rows($checkRepeat) > 0){
    endCode(" CPF já está em uso.", false);
}


for($i = 0; $i < count($turmas); $i++){
    $check = $i->checked;
    $idTurma = $i->id;
    $check_query = mysqli_query($__CONEXAO__, "select id from alunos where turma='$idTurma' and email='$emm'");
    if($check){
        if(mysqli_num_rows($check_query) == 0){
            mysqli_query($__CONEXAO__, "insert into alunos (email, turma) values ('$emm','$idTurma')");
        }
    } else {
        if(mysqli_num_rows($check_query) > 0){
            mysqli_query($__CONEXAO__, "delete from alunos where email='$emm' and turma='$idTurma'");
        }
    }
}

mysqli_query($__CONEXAO__, "update alunos set email='$email' where email='$emm'");
mysqli_query($__CONEXAO__, "update users set nome='$nome', cpf='$cpf', email='$email', nascimento='$nascimento', titularidade='$titularidade', active='$active' where id='$id'");

endCode("Alterado com sucesso", true);