<?php

class NoticiasController extends AppController {
	
	public $name = "Noticias";
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index');
	}
	
	public function admin_index() 
	{
		$this->paginate = array(
			'limit' => 20,
			'order' => array(
				'Noticia.data' =>  'DESC'
			)
		);
		$this->set('noticias', $this->paginate('Noticia'));
	}
	
	public function admin_adicionar() 
	{
	}
	
	public function admin_editar($id = null) 
	{
	}
	
	public function admin_excluir($id, $confirm = false) 
	{
	}
	
	public function index()
	{
		$this->set('noticias', $this->Noticia->find('all'));
	}
	
}
?>