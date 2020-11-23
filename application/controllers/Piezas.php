<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Piezas extends CI_Controller {
    public function __construct(){
		parent::__construct();
		$this->output->enable_profiler(FALSE);       
   	}	

	public function clase(){
        $this->load->model("Piezasmodel");
		$data['piezas'] = $this->Piezasmodel->get_piezas_por_clase($this->uri->segment(3));
		$this->load->view('inicio/top1'); 
		$this->load->view('piezas/piezasclase',$data); 
		$this->load->view('inicio/bottom1');   
	}

	public function index(){		
		//Carga de modelos
        $this->load->model("Tiposclasesequiposmodel");
        $this->load->model("Piezasmodel");
		//Creación de criterio de búsqueda
		$wh = " where (1=1) ";
		$criterio = $this->uri->segment(3,'2');
		$busqueda = $this->uri->segment(4,'');
		$busqueda = str_replace("%20", " ", $busqueda);
		
		if($criterio == "" OR $criterio >=20){
			//Obtención de segmento adicional en la URL (Límite de búsqueda)
			$lim = $this->uri->segment(3,'0');
			//URL sobre la que se paginará
			$config['base_url'] = '';
		}	

		if($busqueda != ""){
			if($busqueda <="20"){
				//Indx puro
			}else{
				if($criterio == 0){
					//Búsqueda por usuario

					//URL sobre la que se paginará
					$config['base_url'] = '/index.php/piezas/index/' .$criterio.'/'.$busqueda.'/';
					//Creación de criterio de búsqueda
					$wh = " where clase='" .$busqueda."'";
					//Obtención de segmento adicional en la URL (Límite de búsqueda)
					$lim = $this->uri->segment(5,'0');
				}else{
					if($criterio == 1){
						//URL sobre la que se paginará
						$config['base_url'] = '/index.php/piezas/index/' .$criterio.'/'.$busqueda.'/';
						//Creación de criterio de búsqueda
						//$wh = " where UPPER(NOMBRE) like UPPER('%" . $busqueda . "%') and sucursal_id = '" .$sucursal."'";
						//Obtención de segmento adicional en la URL (Límite de búsqueda)
						$lim = $this->uri->segment(6,'0');

						$wh = " where descripcion like '%" .$busqueda. "%'";
					}else{
						//Criterio 2 -> Index puro
						$wh = "";
					}
				}
			}
		}else{
			//PAGINACIÓN SIN FILTROS

			$index = $this->uri->segment(2);
			//URL sobre la que se paginará;
			if($criterio == 2){
				if($index == 'index'){
					$config['base_url'] = '/index.php/piezas/index/';
					$lim = $this->uri->segment(3,'0');
				}else{
					$config['base_url'] = 'index/';
					$lim = $this->uri->segment(3,'0');
				}
			}else{
				$config['base_url'] = '/index.php/piezas/index/';
				$lim = $this->uri->segment(3,'0');
			}
		}

		//Si existe búsqueda durante paginación
		if($this->input->post('busca_descripcion')){
			$nombre = $this->input->post('busca_descripcion');
			//Recursividad para iniciar paginación con criterio de búsqueda
			redirect('piezas/index/1/' . $this->input->post('busca_descripcion'));
		}

		$cla = $this->Tiposclasesequiposmodel->get_clases_dropdown();

		$cla[""] = "Seleccionar clase...";

        $data['clases'] = $cla;
		$data['clase'] = (isset($_POST['clase']) ? $_POST['clase'] : "");

		
		$data['result'] = $this->Piezasmodel->get_piezas_where_str($wh,$lim,20);

		//Carga de la librería de paginación para los resultados
		$this->load->library('pagination');
		$arrtot = ( $this->db->query('select count(*) from catpiezas '. $wh . ''));
		//Obtención de array
		$datas = $arrtot->result_array();
		//Declaración del total de filas por enlistar
		$config['total_rows'] = $datas[0]['count'];
		//Resultados por página
		$config['per_page'] = 20; 
		//Inicialización y paso de variable para paginación
		$this->pagination->initialize($config); 

		$this->load->view('inicio/top1'); 
		$this->load->view('piezas/piezasindex',$data); 
		$this->load->view('inicio/bottom1');	
	}	
	
	public function piezasjson()
	{		
        
        $this->load->model("Piezasmodel");

		if ($this->uri->segment(3,'')!="")
			  $wh = " where clase='" . $this->uri->segment(3,'') . "'";
	    else  $wh = "";

	    $data['result'] = $this->Piezasmodel->get_piezas_where_str($wh);

		$this->load->view('piezas/piezasjson',$data); 
	}	
	
	public function agregar() {
		$registro = array(
							 "id"=>"",
							
							 "descripcion_"=>"",
							 "cant_nuevas"=>"0",
							 "clase"=>"",
							 "minimo_nuevas"=>"0",
							 "precio_nuevas" => "0"
							 );
		if ($this->uri->segment(3)!="")
		    $registro['clase'] = $this->uri->segment(3);
		
        $arrt = ( $this->db->query('select clase from clases order by clase'));
        $tipos = ($arrt->result_array());		
		
		$tip = array();
		foreach ($tipos as $tipo) {
			$tip[$tipo["clase"]] = $tipo["clase"];
		}	
		
		$registro['clases'] = $tip;	
		
		$this->load->view('inicio/top1');
		$this->load->view('piezas/detallepieza',$registro); 
		$this->load->view('inicio/bottom1');
		
	}
	
	public function modificar() {
		$this->load->model("Piezasmodel");
		
	    $data = $this->Piezasmodel->get_detalle($this->uri->segment(3));
		$registro = array();
		$registro = $data;
		
		
        $arrt = ( $this->db->query('select clase from clases order by clase'));
        $tipos = ($arrt->result_array());		
		
		$tip = array();
		foreach ($tipos as $tipo) {
			$tip[$tipo["clase"]] = $tipo["clase"];
		}	
		
		$registro['clases'] = $tip;		
		
		//print_r($registro); die(); 
		$this->load->view('inicio/top1');
		$this->load->view('piezas/detallepieza',$registro); 
		$this->load->view('inicio/bottom1');
		
	}	
	
	
	
	public function guardar() {
		$this->load->model("Piezasmodel");
		$this->load->model("Registroaccionesmodel");

	if ($this->session->userdata('nivel')==1) {


		unset($_POST['submit']);
		$accion = $_POST['accion'];
        unset($_POST['accion']);
		
		if ($accion=="i") {
		   $this->Registroaccionesmodel->registrar("NUEVO","catpiezas","ID: " . $_POST['id']);
		   $this->Piezasmodel->agregar_pieza($_POST);
		   redirect('/piezas/'); 
		}
		else {
			$this->Piezasmodel->modificar_pieza($_POST['id'],$_POST);
		   $this->Registroaccionesmodel->registrar("MODIFICACION","catpiezas","ID: " . $_POST['id']);
		 
		   redirect('/piezas/modificar/' . $_POST["id"]); 
		}
		
		}	
       else redirect('/piezas/modificar/' . $_POST["id"]);
	}	
	
public function eliminar() {
	 $this->load->model("Piezasmodel");
	 $this->load->model("Registroaccionesmodel");
     //$arr = ( $this->db->query("select * from piezas where pieza_id='" . $this->uri->segment(3) . "'"));
     //if ($arr->num_rows==0) {
     	$this->Piezasmodel->eliminar_pieza($this->uri->segment(3));
		   $this->Registroaccionesmodel->registrar("ELIMINADO","catpiezas","ID: " . $this->uri->segment(3));
		 
		 redirect('/piezas/'); 
    // }
	// else {
	// 	$data=array("mensaje"=>"No se puede eliminar la pieza. Existen equipos utiliz&aacute;ndola.",
	 //	  "url"=>"/index.php/servicios");
      //  $this->load->view('inicio/top_single1');
	//	$this->load->view('comun/mensaje',$data);
	//	$this->load->view('inicio/bottom1');
		 	
	 	
	 //}
	}
	
	
	public function agregaraequipo() {

		$this->load->model("Piezasmodel");
      
		
	    $data['result'] = $this->Piezasmodel->get_piezas();
		
		
        $arrt = ( $this->db->query('select clase from clases order by clase'));
        $clases = ($arrt->result_array());		
		
		$tip = array();
		foreach ($clases as $clase) {
			$cla[$clase["clase"]] = $clase["clase"];
		}	
		
		$data['clases'] = $cla;		
		
		$arreq =  $this->db->query('select clase from equipos where id=' . $this->uri->segment(3));
	    $eq = ($arreq->result_array());	
		
		$data['clase'] = $eq[0]['clase'];
				

        //$data['pieza_id'] =  $this->uri->segment(3);
        $this->load->view('inicio/top_single1');
		$this->load->view('piezas/agregarpiezaaequipo',$data); 
		$this->load->view('inicio/bottom_single1');
		
	}	
	
	public function guardaraequipo() {
		
		unset($_POST['submit']);
		unset($_POST['tipo']);
		unset($_POST['clase']);
		$accion = $_POST['accion'];
		//$nvaousada = $_POST['nuevaousada'];
        unset($_POST['accion']);
		//unset($_POST['nuevaousada']);

		$this->load->model("Piezasmodel");
		$this->load->model("Registroaccionesmodel");

		$pieza = $this->Piezasmodel->get_detalle($_POST['pieza_id']);

				//print_R($res);
		$_POST['descripcion'] = $pieza['descripcion'];
		$_POST['fecha'] = date('Y-m-d');
		$_POST['hora'] = date('H:i:s');	
		
		if ($accion=="i") {
		   // Actualizar inventarios
		//  if ($_POST['surtida']=="S") { 
		   if ($_POST['tipo_pieza']=="N") {
  	           $this->Piezasmodel->descontar_por_orden($_POST['pieza_id'],$_POST['cantidad'],$_POST['equipo_id']);
  	        }

		  //}				
			// INSERTAR
		   $this->db->insert('piezas',$_POST);

			$this->Registroaccionesmodel->registrar("AGREGAR","piezas-equipo",$_POST['pieza_id'] . " Equipo: " . $_POST['equipo_id']);


		  $this->load->view('inicio/top_single1');
		  $this->load->view('comun/cerrartop'); 
		  $this->load->view('inicio/bottom_single1');
		}
		else {
	     $this->db->where('id', $_POST["id"]);
		 $this->db->update('piezas', $_POST);

			$this->Registroaccionesmodel->registrar("MODIFICACION","piezas-equipo",$_POST['pieza_id'] . " Equipo: " . $_POST['equipo_id']);

		   redirect('/piezas/' . $_POST["id"]); 
		}
		// PARA ACTUALIZAR: $this->db->update('mytable', $data); 
		
	}	

	public function eliminardeequipo() {	
		$this->load->model("Piezasmodel");
		$pieza = $this->Piezasmodel->get_detalle_pieza_equipo($this->uri->segment(3));

		if ($pieza['tipo_pieza']=="N")
            $this->Piezasmodel->reincorporar_por_orden($pieza['pieza_id'],$pieza['cantidad'],$pieza['equipo_id']);

		 $arr =  $this->db->query('delete from piezas where id=' . $this->uri->segment(3));
         $this->load->view('top_single');
	  	 $this->load->view('comun/cerrartop'); 
	  	 $this->load->view('inicio/bottom1');
	}
	
	public function surtir() {
		$this->load->model("Piezasmodel");
		
		$arr = ( $this->db->query("select * from piezas where id='" . $this->uri->segment(3) . "'"));
	    $res = ($arr->result_array());	
		$piezaeq = $res[0];

        $pieza = $this->Piezasmodel->get_detalle($piezaeq['pieza_id']);
		
			        $query = "update catpiezas set cant_nuevas = cant_nuevas-1 where id='" . $pieza['id'] . "'" ;
                    $r     = $this->db->query($query);
					$precio = $pieza['precio_nuevas'];

		  if ($precio=="") 
		     $precio = "0";
	   $arr =  $this->db->query("update piezas set surtida='S', precio=" . $precio . " where id=" . $this->uri->segment(3));
			
		
		redirect('/equipos/modificar/' . $piezaeq["equipo_id"]); 
		
	}
	
    public function pdf()
    {
    	$this->load->model("Serviciosmodel");
    $this->load->helper('dompdf');
    $data['result'] = $this->Serviciosmodel->get_servicios_where();
	
	$html =	 $this->load->view('servicios/serviciospdf',$data,true); 	
	
    //$html = "<html><body>aaa</body></html>";
    pdf_create($html, 'servicios');
    }	
    
    public function agregarpiezas() {
    	 $arrt = ( $this->db->query('select clase from clases order by clase'));
        $clases = ($arrt->result_array());		
		
		$tip = array();
		$cla = array(""=>"Seleccionar clase");  
		foreach ($clases as $clase) {
			$cla[$clase["clase"]] = $clase["clase"];
		}	
		
		$data['clases'] = $cla;		
		
    	$this->load->view('inicio/top1');
		$this->load->view('piezas/agregarpiezas',$data); 
		$this->load->view('inicio/bottom1');
		
    }	
	
	function guardarpiezas(){
        $this->load->model("Piezasmodel");		
		$mens = ""; $mi = 0;
		for ($i=0;$i<=20;$i++) {
		    if ($_POST['id' . $i]!="") { // SI EXISTE
		      $nr = $this->Piezasmodel->num_piezas($_POST['id'.$i]);
             			    
		    	if (($_POST['descripcion' . $i] !="") &&
		    	   ($_POST['clase' . $i] !="") && ($nr==0)) {
				     $po = array(
				          "id"=>$_POST["id" . $i],
				          "descripcion"=>$_POST["descripcion" . $i],
				          "precio_nuevas"=>$_POST["precio_nuevas" . $i],
				          "cant_nuevas"=>$_POST["cant_nuevas" . $i],
				          "minimo_nuevas"=>$_POST["minimo_nuevas" . $i],
				          "clase"=>$_POST["clase" . $i]
						  );
				        $this->Piezasmodel->agregar_pieza($po);
						//$this->db->insert("catpiezas",$po);
						$mens .= $po['id'] . "<br>";
						$mi++;
				   }
		    }	
		}

       if ($mi>0)
	      $mens = "Se insertaron " . $mi . " registros:<br>" . $mens;
	   else $mens = "No se insertaron registros.";
			$this->load->view('inicio/top1');
		$this->load->view('comun/mensaje',array("mensaje"=>$mens,"url"=>"/index.php/piezas")); 
		$this->load->view('inicio/bottom1');
	}
	
	public function guardarmodificacionespiezas() {
		$this->load->model("Piezasmodel");

	if (isset($_POST['precio_nuevas0'])) {
		for ($j=0;$j<=$_POST['j'];$j++) {
			$p = array("precio_nuevas"=>$_POST["precio_nuevas" . $j]
			         );
		 $this->Piezasmodel->modificar_pieza($_POST["id" . $j],$p); 	
		 }
	}
		 
		redirect('piezas/index');
		 
		
	}
	
	public function consultarexistencias() {
		$this->load->model("Piezasmodel");

		 $this->load->library('pagination');
        $config['base_url'] = '/index.php/piezas/consultarexistencias/' . $this->uri->segment(3) .
		   "/" . $this->uri->segment(4);

        $config['total_rows'] = $this->Piezasmodel->num_piezas_clase($this->uri->segment(3));

        $config['per_page'] = 12; 
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);				
		
		  $lim = $this->uri->segment(5,'0');
				
        $piezas = $this->Piezasmodel->get_piezas_clase($this->uri->segment(3),$lim,12);
		$equipo_id = $this->uri->segment(4);
		$registro['result'] = $piezas;
		$registro['equipo_id'] = $equipo_id;
		$registro['clase'] = $this->uri->segment(3);
		$this->load->view('inicio/top_single1');
		$this->load->view('piezas/consultarexistencias',$registro); 
		$this->load->view('inicio/bottom_single1');
			
		
	}

	public function consultamovimientos(){
		$this->load->model("Piezasmodel");

	    $this->load->library('pagination');



		$anio = $this->uri->segment(3,"");
    	$mes  = $this->uri->segment(4,"");
    	if ($anio=="") {
    		$anio = date("Y");
    		$mes = date("m");
    	}

        $config['base_url'] = '/index.php/piezas/consultamovimientos/' . $anio . "/" . $mes;
     
        // array_slice



		$movimientos = $this->Piezasmodel->consulta_movimientos_por_mes($anio,$mes);

        $config['total_rows'] = count($movimientos);
        $config['per_page'] = 10; 
        $config['uri_segment'] = 5;

		$this->pagination->initialize($config);		


		$registro['movimientos'] = array_slice($movimientos,$this->uri->segment(5,0) ,10);
		$this->load->view('inicio/top1');
		$this->load->view('piezas/consultarmovimientos',$registro); 
		$this->load->view('inicio/bottom1');
	}
	
  // -------------------- REPORTES

	public function usadaspordia() {
		if ($this->uri->segment(3,"")!="") {
		    $a=$this->uri->segment(3);
		    $m=$this->uri->segment(4);
		    $d=$this->uri->segment(5);
		}
		else
		{
			$a = date("Y");
			$m = date("m");
			$d = date("d");
		}
		$fecha = $a . "-" . $m . "-" . $d;
		//echo $fecha; die();

		$arrt = $this->db->query("select piezas.id,piezas.pieza_id,piezas.equipo_id," . 
							"piezas.descripcion,piezas.cantidad,piezas.tipo_pieza,piezas.fecha,piezas.hora," .
							"equipos.num_orden from piezas inner join equipos on piezas.equipo_id=equipos.id " .
							" where fecha='" . $fecha . "'");
        $piezas = $arrt->result_array();
		$registro['piezas'] = $piezas;
		$registro['fecha']  = $d . "/" . $m . "/" . $a;
		$this->load->view('inicio/top1');
		$this->load->view('piezas/usadaspordia',$registro); 
		$this->load->view('inicio/bottom1');
	}

	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */