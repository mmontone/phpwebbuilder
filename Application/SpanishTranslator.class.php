<?php

require_once dirname(__FILE__) . '/Translator.class.php';

class SpanishTranslator extends Translator
{
   	function SpanishTranslator() {
   		parent::Translator();
   	}

    function dictionary() {
    	return array (
            'Are you sure you want to save?'   => '¿Estas seguro que quieres guardar?',
            'Are you sure you want to delete?' => '¿Estas seguro que quieres borrar?',
            'Are you sure that you want to delete the object?' => '¿Estás seguro de que quieres borrar el objeto?',
            'Edit' => 'Editar',
            'Add' => 'Agregar',
            'Delete' => 'Borrar',
            'Class' => 'Clase',
            'Object of class' => 'Objeto de clase',
            'Yes' => 'Si',
            'Class' => 'Clase',
            'Showing' => 'Mostrando',
            'per page' => 'por página',
            'elements available' => 'elementos disponibles',
            'refresh' => 'actualizar'
            );
    }
}
?>