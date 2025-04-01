<?php

return [
    'GET' => [
        '/' => 'ProductController@index',
        '/products' => 'ProductController@index',
        '/create' => 'ProductController@create',
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