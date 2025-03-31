<?php 
include '../model/Product.php';;

class ProductController {
    private $productModel;

    public function __construct($pConn) {
        $this->productModel = new Product($pConn);
    }

    public function getAllProducts() {
        
    }

    public function getProductById($pId) {
        
    }

    public function getProductsByCategory($pCategoryId) {
        
    }

    public function getProductsBySearch($pSearch) {
        
    }

    public function getProductsByPriceRange($pMinPrice, $pMaxPrice) {
        
    }

    public function cadastraProduto($pNome, $pDescricao, $pPreco, $pQuantidade, $pCategoriaId) {
        
    }

    public function atualizaProduto($pId, $pNome, $pDescricao, $pPreco, $pQuantidade, $pCategoriaId) {
        
    }

    public function excluiProduto($pId) {
        
    }

}