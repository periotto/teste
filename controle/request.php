<?php

/* PAGINA RESPONSAVEL PELA TRASIวรO ENTRE OS LINKS */

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['id_user'])) {
    $pagina = $_GET['PAGINA'];
    switch ($pagina) {
/*--------------------- Paginas ----------------------------------------------*/
        case 'vendas' :
            header("Location: ../web/vendas.html");
            break;
        case 'produtos' :
            header("Location: ../web/produtos.html");
            break;
        case 'franquias' :
            header("Location: ../web/franquias.html");
            break;
        case 'lojas' :
            header("Location: ../web/lojas.html");
            break;
        case 'usuarios' :
            header("Location: ../web/usuarios.html");
            break;
        case 'sair' :
            session_destroy();
            header("Location: ../index.html");
            break;
    }
} else {
    header("Location: ../index.html");
}   