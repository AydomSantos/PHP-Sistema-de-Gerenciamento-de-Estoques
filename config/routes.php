<?php

return [
    'GET' => [
        '/' => 'ProductController@index',
        '/products' => 'ProductController@index',
        '/adicionar' => 'ProductController@adicionar',
        '/edit/{id}' => 'ProductController@edit',
        '/view/{id}' => 'ProductController@view',
    ],
    'POST' => [
        '/create' => 'ProductController@cadastrarProduto',
        '/edit/{id}' => 'ProductController@editarProduto',
        '/delete/{id}' => 'ProductController@excluirProduto',
    ],
];

?>