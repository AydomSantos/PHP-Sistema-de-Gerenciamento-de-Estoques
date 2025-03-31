<?php 

class Product {
    private $vConn;

    public function __construct($conn) {
        $this->vConn = $conn;
    }

    public function getAllProducts() {
        
    }

    public function getProductById($pId) {
        
    }

    public function getProductsByCategory($pCategoryId) {
        
    }

    public function getProductsBySearch($pSearchTerm) {
        
    }

    public function getProductsByPriceRange($pMinPrice, $pMaxPrice) {
        
    }

    public function adcionaProduto($pNome, $pDescricao, $pPreco, $pCategoriaId) {
        
    }

    public function atualizaProduto($pId, $pNome, $pDescricao, $pPreco, $pCategoriaId) {
        
    }

    public function removeProduto($pId) {
        
    }
}