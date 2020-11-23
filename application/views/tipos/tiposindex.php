<script language="javascript">
$("a#regresar").attr('href', '/index.php/inicio/administracion');

$(function () {
   $("#ddclase").change(function() {
      top.location.href='/index.php/tipos/index/' + $("#ddclase").val();
   });
});


</script>
<div align="center" style="height:50px">
	Seleccionar equipo: <?php echo form_dropdown("clase",$clases,$clase,"id='ddclase'"); ?>
</div>

<div align="center">
<table width="100%" id="tablatipos" class="cell-border stripe compact hover">
	<thead>
<tr>
<th>Modelo</th>
<th>Descripci&oacute;n</th>
<th>Equipo</th>
<th>Ver características del modelo</th>
<th>Característica</th>
<?php
 if ($this->session->userdata('nivel')==1) 	
	echo "<th>Eliminar</th>";		
?>

</tr>
</thead>
<tbody>
<?php
foreach ($result as $item) {
	$subtipos = "| ";
	foreach ($item['subtipos'] as $itemsubtipo) {
      $subtipos .= $itemsubtipo['capacidad'] . " |";
    }
	echo "<tr>";
	echo "<td style='height:35px;'><a href='/index.php/tipos/modificar/" . $item['tipo'] . "'>" . $item['tipo'] . "</td>";
	echo "<td>" . $item['descripcion'] . "</td>";
	echo "<td>" . $item['clase_descripcion'] . "</td>";
	echo "<td align='center'>";
	echo "<a class='button button-primary' href='/index.php/subtipos/index/1/" . $item['tipo'] . "'>" . $subtipos . "</a></td>";

	echo "<td align='center'>";
	echo "<a class='button button-primary' href='/index.php/subtipos/agregar/" . $item['tipo'] . "'>Añadir características</a></td>";
 if ($this->session->userdata('nivel')==1) 	
	echo "<td align='center'><a href='/index.php/tipos/eliminar/" . $item['tipo'] . "'><img src='/images/ico_eliminar.png'></a></td>";		
	echo "</tr>";	
}
?>
</tbody>
</table>

<div><a class="button button-primary" href="/index.php/tipos/agregar">Agregar modelo</a></div>
</div>


<script>
$(document).ready(function() {
    $('#tablatipos').DataTable( {
    	paging:false,
    	searching:false,
    	ordering:false,
    	info: ""
     });
} );
</script>
