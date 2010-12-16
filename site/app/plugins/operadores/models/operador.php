<?php
class Operador extends OperadoresAppModel 
{
	public $name = 'Operador';
	public $useTable = 'operadores';
	
	public function equalFields($data, $field1, $field2) 
	{
		return $data[$field1] == $this->data['Operador'][$field2];
	}
}
?>