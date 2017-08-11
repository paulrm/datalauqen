<html>
<head>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<?PHP
echo "datalaquen working ...";
if(isset($_REQUEST["action"]))
	$action=$_REQUEST["action"];
else
	$action="none";
	
echo "<br>";
echo "<a href=\"index.php?action=clean\">01 clean</a><br>";
echo "<a href=\"index.php?action=init\">02 init</a><br>";
echo "<a href=\"index.php?action=init\">03 create xlsx from db</a><br>";
echo "<a href=\"index.php?action=init\">03 donwload xlsx</a><br>";
echo '
<!-- El tipo de codificación de datos, enctype, DEBE especificarse como sigue -->
<form enctype="multipart/form-data" action="index.php" method="POST" style="margin-bottom: 0px;">
    <!-- MAX_FILE_SIZE debe preceder al campo de entrada del fichero -->
    04 upload <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- El nombre del elemento de entrada determina el nombre en el array $_FILES -->
    Enviar este fichero: <input name="fichero_usuario" type="file" />
    <input type="submit" value="Enviar fichero" />
</form>
';
echo "<a href=\"index.php?action=init\">05 process xlsx</a><br>";

/*
echo "<a href=\"test-pgsql.php\">test pgsql</a><br>";
echo "<a href=\"test-write-xls.php\">test write xls</a><br>";
echo "<a href=\"test-write-xls.xlsx\">Download xlsx file</a><br>";
*/
switch ($action) {
    case "clean":
        clean();
        break;
    case "init":
        init();
        break;
    case 2:
        echo "i es igual a 2";
        break;
	default:
		break;
}


$ndata=getNdata();
if($ndata>0)
	{
	echo "<hr>";
	echo '<div id="chart_div"></div>';
	echo "<hr>";
	}

echo "<pre>_FILES=" . print_r($_FILES, true) . "</pre>";


if(isset($_FILES["fichero_usuario"]))
	{

if (is_uploaded_file($_FILES['fichero_usuario']['tmp_name'])) 
	{
   echo "Archivo ". $_FILES['fichero_usuario']['name'] ." uploaded.\n";
   //echo "Monstrar contenido\n";
   //readfile($_FILES['fichero_usuario']['tmp_name']);
	$dir_subida = 'tmp/';
	$fichero_subido = $dir_subida . basename($_FILES['fichero_usuario']['name']);

	if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) 
		{
		echo "Upload and move OK.\n";
		} 
	else
		{
		echo "Upload Failed!\n";
		}   
	} 
	else 
		{
   		echo "upload Failed: ";
   		echo "nombre del archivo '". $_FILES['fichero_usuario']['tmp_name'] . "'.";
		}
	}



	



echo "<hr><h4>datalauqen v0.0</h4>";
$script01 = "
<script>
  google.charts.load('current', {'packages':['line', 'corechart']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var button = document.getElementById('change-chart');
      var chartDiv = document.getElementById('chart_div');

      var data = new google.visualization.DataTable();
      data.addColumn('date', 'time');
      data.addColumn('number', 'data01');
      data.addColumn('number', 'data02');
";
$script02 = getData();
$script03 = "
      var materialOptions = {
        chart: {
          title: 'title of data'
        },
        width: 900,
        height: 500,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'data01 axis'},
          1: {axis: 'data02 axis'}
        },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            Temps: {label: 'data01 label (measure)'},
            Daylight: {label: 'data02 label'}
          }
        }
      };

 
      function drawMaterialChart() {
        var materialChart = new google.charts.Line(chartDiv);
        materialChart.draw(data, materialOptions);
        button.innerText = 'Change to Classic';
        button.onclick = drawClassicChart;
      }

      function drawClassicChart() {
        var classicChart = new google.visualization.LineChart(chartDiv);
        classicChart.draw(data, classicOptions);
        button.innerText = 'Change to Material';
        button.onclick = drawMaterialChart;
      }

      drawMaterialChart();

    }
</script>
";
if($ndata>0)
	{
	echo $script01 . $script02 . $script03;
	}
?>
</body>
</html>
<?PHP
//----------------------------------------------------------------------------------------
function clean(){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=ato6px4")
    or die('No se ha podido conectar: ' . pg_last_error());
$query = 'TRUNCATE TABLE test';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
pg_free_result($result);
return;
}
//----------------------------------------------------------------------------------------
function init(){
clean();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=ato6px4")
    or die('No se ha podido conectar: ' . pg_last_error());	
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
//pg_free_result($result);
return;
}
//----------------------------------------------------------------------------------------
function getNdata(){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=ato6px4")
    or die('No se ha podido conectar: ' . pg_last_error());
$query = 'SELECT count(*) FROM test';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
$data = pg_fetch_array($result, null, PGSQL_ASSOC);
$count = $data["count"];
//echo "<pre>data=" . print_r($data, true) . "</pre>";
pg_free_result($result);
return $count;
}
//----------------------------------------------------------------------------------------
function getData(){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $ndata;

// Conectando y seleccionado la base de datos  
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=ato6px4")
    or die('No se ha podido conectar: ' . pg_last_error());

// Realizando una consulta SQL
$query = 'SELECT * FROM test  order by fecha';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

// Imprimiendo los resultados en HTML
// [new Date(2014, 0),  -.5,  5.7],
$out = "";
$out .= "data.addRows([\n";

$j=$ndata;
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	
	//echo "/* line=" . print_r($line, true) ." */\n";
	$fecha=$line["fecha"];
	list($y,$m,$d) = explode("-",$fecha);
	$valor=$line["valor"];
	
    $out .=  "[new Date($y, $m, $d),  $valor,  $j],\n";
	$j--;
    //foreach ($line as $col_value) {
    //    echo "\t\t<td>$col_value</td>\n";
    //}

}
    //$out .= " [new Date(2014, 11), -.2,  4.5]\n";
	$out .= " ]);\n";

// Liberando el conjunto de resultados
pg_free_result($result);
// Cerrando la conexió
return $out;
}



?>
