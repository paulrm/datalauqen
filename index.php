<html>
<head>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<?PHP

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Argentina/Buenos_Aires');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');


echo "<a href=\"index.php\">datalaquen</a> working ...";
if(isset($_REQUEST["action"]))
	$action=$_REQUEST["action"];
else
	$action="none";
	
echo "<br>";
echo "<a href=\"index.php?action=clean\">01 clean</a><br>";
echo "<a href=\"index.php?action=init\">02 init</a><br>";
echo "<a href=\"index.php?action=create\">03 create xlsx from db</a><br>";
echo "<a href=\"tmp/example.xlsx\">03 donwload xlsx</a><br>";
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
echo "<a href=\"index.php?action=process\">05 process xlsx</a><br>";

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
    case "create":
        create();
        break;
    case "process":
        process();
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

//echo "<pre>_FILES=" . print_r($_FILES, true) . "</pre>";


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
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
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
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
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
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
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
global $ndata;

// Conectando y seleccionado la base de datos  
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
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
//---------------------------------------------------------------------------------------
function getData2array(){
global $ndata;

// Conectando y seleccionado la base de datos  
$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
    or die('No se ha podido conectar: ' . pg_last_error());
// Realizando una consulta SQL
$query = 'SELECT * FROM test  order by fecha';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
$data = array();
$j=$ndata;
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	//echo "/* line=" . print_r($line, true) ." */\n";
	$fecha=$line["fecha"];
	$valor=$line["valor"];
	$data[$fecha] = $valor;
	$j--;
}
pg_free_result($result);
return $data;
}
//---------------------------------------------------------------------------------------
function create() {
/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';


// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
$author = "Paul Messina";
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator($author)
							 ->setLastModifiedBy($author)
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");


// Add some data
echo date('H:i:s') ." Add some data date=" . date(DATE_RFC2822) . EOL;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Fecha')
            ->setCellValue('B1', 'Valor');

$styleArray = array(
    'font' => array(
        'bold' => true,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startcolor' => array(
            'argb' => 'FFA0A0A0',
        ),
        'endcolor' => array(
            'argb' => 'FFFFFFFF',
        ),
    ),
);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', 'INVAP SE')
							  ->getStyle('L1')->applyFromArray($styleArray);
							  
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', date(DATE_RFC2822))
							  ->getStyle('L2')->applyFromArray($styleArray);            				 	


$data = getData2array();
$i=2;
foreach($data as $k => $v) 
	{
	echo "grabando $k => $v <br>";
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $k);		
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B' . $i, $v);
	$i++;		
	}
// Miscellaneous glyphs, UTF-8
/*
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Miscellaneous glyphs')
            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
*/

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('INVAP Data');




// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$objWriter->save('tmp/example.xlsx');
# $objWriter->save(str_replace('.php', '.xlsx', __FILE__));

$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;

// Save Excel 95 file
/*
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
 */
  
  
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;	
}
//---------------------------------------------------------------------------------------
function process() {
/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel/Reader/Excel2007.php';

// Cargando la hoja de cálculo
$objReader = new PHPExcel_Reader_Excel2007();
$objPHPExcel = $objReader->load("tmp/example.xlsx");
$debug = "";

        $objPHPExcel->setActiveSheetIndex(0);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        $i=1;
        foreach($sheetData as $row => $fila)
            {
            $debug .=  "i=$i " . print_r($fila, true);
            if($fila["A"]!="" && $fila["A"]!="Fecha")
            	{
                echo "<pre>";
                echo print_r($fila,true);
                echo "</pre>";
            	
				$fecha=$fila["A"];
				$valor=$fila["B"];
				upsert($fecha, $valor);
				}  
			$i++;    
        } // End-Foreeach




// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done reading file" , EOL;
	
}
//---------------------------------------------------------------------------------------
function upsert($fecha, $valor) {
	$dbconn = pg_connect("host=localhost dbname=mydb user=postgres password=qhJdvBSk")
	or die('No se ha podido conectar: ' . pg_last_error());	

	$insert = "
				INSERT INTO test (fecha, valor) VALUES ('$fecha', $valor ) 
				ON CONFLICT (fecha) DO UPDATE SET valor = $valor WHERE fecha = '$fecha';
			";
	$insert = "
				SELECT merge_db('$fecha', $valor )
			";	
	echo "<hr>" . $insert . "<hr>";
	pg_query($insert) or die('La consulta fallo: ' . pg_last_error());
	return;
}

/*

INSERT INTO distributors (did, dname) VALUES (7, 'Redline GmbH')
    ON CONFLICT (did) DO NOTHING;

INSERT INTO distributors AS d (did, dname) VALUES (8, 'Anvil Distribution')
    ON CONFLICT (did) DO UPDATE
	
	
CREATE TABLE public.test2
(
    fecha date,
    valor numeric(5, 2),
    PRIMARY KEY (fecha)
)
WITH (
    OIDS = FALSE
);

ALTER TABLE public.test2
    OWNER to postgres;
*/
?>
