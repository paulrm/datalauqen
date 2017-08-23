<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" />
</head>
<body>

<h4>Editor v1.0</h4>
<button id="addRow">Add new row</button>
<hr />



<?PHP


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectando y seleccionado la base de datos  
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
    or die('No se ha podido conectar: ' . pg_last_error());

// Truncate Table






// Realizando una consulta SQL
$query = 'SELECT * FROM test order by fecha';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

// Imprimiendo los resultados en HTML

$table = "";
$table = '<table id="example" class="display" cellspacing="0" width="100%">' ."\n";
$table .= '<thead>
            <tr>
                <th>Fecha</th>
                <th>Valor</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Fecha</th>
                <th>Valor</th>
                <th>Notas</th>
            </tr>
        </tfoot>';

$i = 1;
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    
	//echo "<pre>" . print_r($line,true) . "</pre>";
	$table .= "\t<tr>\n";
    /*
	foreach ($line as $col_value) {
        $table .= "\t\t<td>$col_value</td>\n";
    }
    */
	$table .= "\t\t<td>" . '<input type="text" id="data-A' .$i . '" name="data-A' .$i . '" value="' . $line["fecha"] . '">' . "</td>\n";
	$table .= "\t\t<td>" . '<input type="text" id="data-B' .$i . '" name="data-B' .$i . '" value="' . $line["valor"] . '">' . "</td>\n";
	$table .= "\t\t<td>" . '<input type="text" id="data-C' .$i . '" name="data-C' .$i . '" value="' . "" . '">' . "</td>\n";
	$table .= "\t</tr>\n";
	$i++;
}
$table .= "</table>\n";

echo $table;
// Liberando el conjunto de resultados

pg_free_result($result);

// Cerrando la conexión
pg_close($dbconn);

?>
<button id="submit">Submit</button>
<script language="javascript">
$(document).ready(function() {
    var table = $('#example').DataTable( { 
	"lengthMenu": [[20, 50, -1], [20, 50, "All"]]
	} );
	

    $('#submit').click( function() {
        var data = table.$('input, select').serialize();
        alert(
            "The following data would have been submitted to the server: \n\n"+
            data.substr( 0, 120 )+'...'
        );
        return false;
    } );
	
	var counter = 1;

    $('#addRow').on( 'click', function () {
        table.row.add( [
            '<input type="text" id="data-A1" name="ndata-A' + counter +'" value="' + counter +'.1' +  '">',
            '<input type="text" id="data-B1" name="ndata-B' + counter +'" value="' + counter +'.2' +  '">',
            '<input type="text" id="data-C1" name="ndata-C' + counter +'" value="' + counter +'.3' +  '">'
        ] ).draw( false );
 
        counter++;
    } );
 
    // Automatically add a first row of data
    //$('#addRow').click();	
	
} );
</script>
</body>
</html>