<?PHP

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectando y seleccionado la base de datos  
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=ato6px4")
    or die('No se ha podido conectar: ' . pg_last_error());

// Truncate Table


	$insert = "Truncate table test;" ;
	 pg_query($insert) or die('La consulta fallo: ' . pg_last_error());


// Insert data

$data = array ( "2017-08-01" =>	95.90 ,
				"2017-08-02" => 95.6,
				"2017-08-03" => 96.0,
				"2017-08-04" => 96.7,
				"2017-08-05" => 96.4,
				"2017-08-06" => 95.9,
				);
foreach($data as $k => $v )
	{			
	$insert = "INSERT INTO test ( fecha, valor) VALUES ( '$k',$v);" ;
	 pg_query($insert) or die('La consulta fallo: ' . pg_last_error());
	}








// Realizando una consulta SQL
$query = 'SELECT * FROM test order by fecha';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

// Imprimiendo los resultados en HTML
echo "<table>\n";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";

// Liberando el conjunto de resultados

pg_free_result($result);

// Cerrando la conexión
pg_close($dbconn);
?>