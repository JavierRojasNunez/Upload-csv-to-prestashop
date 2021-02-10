<?php
header("Content-Type: text/html;charset=utf-8");
define('_PS_ADMIN_DIR_', getcwd());
include_once(_PS_ADMIN_DIR_ . '/../config/config.inc.php');

$context = Context::getContext();
$employee = new Employee(1);
$context->employee = $employee;

/* 
*leo el csv para cambiar el ";" por la "," y viceversa (al volverlo a montar) para no tener problemas con la importación
* si no, tuve que modificar el codigo interno de prestashop en concreto
* la clase adminImportController() para que funcionase. a demas de cambiar el
* valor del IVA a su id_iva..
*/

$fp = fopen('import/products_final.csv', 'w');
if (($gestor = fopen("import/products.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {

        $datos2 = preg_replace('/;/', ',', $datos);//cambio a formato de separadores de prestashop por defecto

        if($datos2[5] == 21) $datos2[5] = 1; //cambio valor por id_iva
        if($datos2[5] == 10) $datos2[5] = 2;
        if($datos2[5] ==  4) $datos2[5] = 3;
        
        fputcsv($fp, $datos2, ';', chr(0));
    }
    
    fclose($gestor);
    fclose($fp);

}

function loadProductsPost() {

    $_POST = array (
        'tab' => 'AdminImport',
        'forceIDs' => '0',
        'skip' => '1',
        'csv' => 'products_final.csv',
        'entity' => '1',
        'separator' => ';',
        'multiple_value_separator' => ',',
        'iso_lang' => 'es',
        'convert' => '',
        'import' => 'Importar datos csv',
        'type_value' => array(
            0 => 'name',
            1 => 'reference',
            2 => 'ean13', 
            3 => 'price_tex',
            4 => 'Wholesale_price',
            5 => 'id_tax_rules_group',
            6 => 'quantity',
            7 => 'category',
            8 => 'manufacturer',


        ),
    );
}


$import = New AdminImportControllerCore();
loadProductsPost();
echo ($import->productImport()) ? 'Productos importados a la BBDD con éxito' : 'Falló la importación de productos a la BBDD';
