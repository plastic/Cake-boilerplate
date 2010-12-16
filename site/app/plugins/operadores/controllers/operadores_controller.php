<?php

class OperadoresController extends OperadoresAppController {
	
	public $name = 'Operadores';
	
	public function admin_index() 
	{
		$this->Session->delete('Message');
	}
	
	public function admin_operadores() 
	{
		$this->set('operadores', $this->Operador->find('all', array('conditions' => array('Operador.loja_id is not null'))));
	}
	
	public function admin_adicionar() 
	{
		$this->Operador->validate = array(
			'name' => array(
				'rule' => 'notEmpty'
			),
			'email' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty'
				),
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'E-mail já cadastrado'
				),
				'email' => array(
					'rule' => 'email',
					'message' => 'Digite um e-mail valido'
				)
			),
			'senha' => array(
				'rule' => array('equalFields', 'senha', 'confirm'),
				'message' => 'Senhas diferentes'
			)
		);
		
		if (!empty($this->data)) {
			$this->data['Operador']['confirm'] = $this->Auth->password($this->data['Operador']['confirm']);
			if ($this->Operador->save($this->data)) {
				$this->Session->setFlash(__('Operador adicionado com sucesso!', true), 'default', array('success' => true));
				$this->redirect('/admin/operadores/operadores');
			} else {
				$this->Session->setFlash(__('Operador não adicionado! Verifique os campos e tente novamente.', true), 'default', array('error' => true));
			}
			$this->data['Operador']['senha'] = $this->data['Operador']['confirm'] = null;
		}
	}
	
	public function admin_editar($id) 
	{
		$this->Operador->id = $id;
		
		if (empty($this->data)) {
			$this->data = $this->Operador->read();
			unset($this->data['Operador']['senha']);
		} else {
			
			if(empty($this->data['Operador']['confirm'])) {
				unset($this->data['Operador']['senha']);
				unset($this->data['Operador']['confirm']);
			}
			
			if( !isset($this->data['Operador']['senha']) || empty($this->data['Operador']['senha']) ) {
				
				$this->Operador->validate = array(
					'name' => array(
						'rule' => 'notEmpty'
					),
					'email' => array(
						'notEmpty' => array(
							'rule' => 'notEmpty'
						),
						'email' => array(
							'rule' => 'email',
							'message' => 'Digite um e-mail valido'
						)
					)
				);
				
			} else {
				
				$this->Operador->validate = array(
					'name' => array(
						'rule' => 'notEmpty'
					),
					'email' => array(
						'notEmpty' => array(
							'rule' => 'notEmpty'
						),
						'email' => array(
							'rule' => 'email',
							'message' => 'Digite um e-mail valido'
						)
					),
					'senha' => array(
						'rule' => array('equalFields', 'senha', 'confirm'),
						'message' => 'Senhas diferentes'
					)
				);
			}
			
			if( !empty($this->data['Operador']['confirm']) ) {
				$this->data['Operador']['confirm'] = $this->Auth->password($this->data['Operador']['confirm']);
			}
			
			if ($this->Operador->save($this->data)) {
				$this->Session->setFlash(__('Operador alterado com sucesso!', true), 'default', array('success' => true));
				$this->redirect('/admin/operadores/operadores');
			} else 
				$this->Session->setFlash(__('Operador não alterado. Verifique os dados e tente novamente!', true), 'default', array('error' => true));
				
			unset($this->data['Operador']['senha']);
			unset($this->data['Operador']['confirm']);
		}
	}
	
	public function admin_excluir($id, $confirm = false) 
	{
		if ($confirm !== false)
		{
			if ($this->Operador->delete($id))
			{
				$this->Session->setFlash(__('Operador deletado com sucesso!', true), 'default', array('success' => true));
				$user = $this->Auth->user();
				if ($id == $operator['Operador']['id'])
					$this->redirect($this->Auth->logout());
				$this->redirect('/admin/operadores/operadores');
			} else
				$this->Session->setFlash(__('Operador não deletado. Verifique os dados e tente novamente!', true), 'default', array('error' => true));
		}
		$this->set(compact('id'));
	}
	
	public function admin_login() 
	{
		if ( $this->Auth->user() )
			$this->redirect($this->Auth->loginRedirect);
			
		if ($this->Session->check('Message.auth'))
			$this->set('error', $this->Session->read('Message.auth.message'));
	}
	
	public function admin_logout() 
	{
		$this->autoRender = false;
		$this->redirect($this->Auth->logout());
	}
}
?>