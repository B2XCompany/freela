<?php
include '../../../conexao.php';

justLog($__EMAIL__, $__TYPE__, 1);

$_query_ = mysqli_query($__CONEXAO__, "select * from categorias");

$array = array();

while($dados = mysqli_fetch_array($_query_)){
    $nome = $dados["nome"];
    $status = $dados["active"];

    $status = $status == '1' ? "active" : "inactive";

    $query = mysqli_query($__CONEXAO__, "select id from turmas where categoria='$nome'");

    $arr = array(
        "id"        => $dados["id"], 
        "nome"      => decrypt($nome), 
        "status"    => $status,
        "turmas"    => mysqli_num_rows($query)
    );

    array_push($array, $arr);
}

endCode($array, true);