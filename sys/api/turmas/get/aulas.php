<?php
include '../../../conexao.php';

justLog($__EMAIL__, $__TYPE__, 0);

$complemento = '';

if($__TYPE__ == 2){
    $_query_ = mysqli_query($__CONEXAO__, "select * from aulas");
} else {
    $table = 'alunos';
    if($__TYPE__ == 1){
        $table = 'professores';
    }
    $query = mysqli_query($__CONEXAO__, "select * from $table where email='$__EMAIL__'");
    $turmas = '';
    while($getQuery = mysqli_fetch_array($query)){
        $value = $getQuery['turma'];
        $valuedec = decrypt($value);
        $turmas .= $valuedec . ' , ';
    }
    $turmas = substr($turmas, 0, -3);
    $_query_ = mysqli_query($__CONEXAO__, "select * from aulas where turma in ($turmas)");
}

$array = array();

while($dados = mysqli_fetch_array($_query_)){
    $turmaId = decrypt($dados["turma"]);

    $data = $dados["data"];

    $queryT = mysqli_query($__CONEXAO__, "select nome from turmas where id='$turmaId'");

    $turma = mysqli_fetch_assoc($queryT)["nome"];

    $idC = encrypt($dados["id"]);

    $arr = array(
        "id"        => $dados["id"], 
        "nome"      => $nome,
        "turma"     => decrypt($turma),
        "data"      => $data,
        // "turmas"        => mysqli_num_rows($_query_),
        "status"    => $status
    );
    array_push($array, $arr);
}

endCode($array, true);