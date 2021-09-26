<?php
    class Huffman {
        
        //Declaracion variables
        public static $bits = array("");
        public $dataOrden = array("");
        public static $letras,$existe,$tablaFinal,$tabla;
        public static $mensaje,$bitsDatos,$subCaracteres,$subFrecuencia;
        public $cabezaArbol,$arbol,$datosSeparados,$datosSeparados2;
        public $codigo,$letra,$codigoLetra,$tamaño,$comprimido,
            $ahorrado,$codificado,$bitsInicial,$mensajeCodificado,$saltos;

        //Constructor
        function __construct($m){
            Huffman::$mensaje = $m;
        }

        //Funcion que inicializa la codificacion por cursores
        public function iniciar(){
            //Creacion de la tabla
            Huffman::$subCaracteres = array();
            for($i=0;$i < strlen(Huffman::$mensaje);$i++){
                $c = $this->charAt(Huffman::$mensaje,$i);
                $this->frecuencia($c);
            }
            //Creacion basica del cursor: padre, hijo der/izq y codigo bin
            Huffman::$subCaracteres = $this->ordenar(Huffman::$subCaracteres);
            $this->tamaño = $this->inicializar(Huffman::$subCaracteres);
            $this->crearMatriz($this->tamaño);
            $this->llenar($this->tamaño);
            $this->completar();
            $this->crearOrden("!",1,1,1,"");
            //Creacion del arbol
            $this->cabezaArbol = $this->tamaño-1;
            $this->arbol = $this->realizarArbol($this->cabezaArbol);
            //Completar tabla HTML
            $this->actualizarTabla($this->tamaño);
            $this->terminarTabla($this->tamaño);
            //Creacion del string informacion de codificacion en bits
            Huffman::$bitsDatos = $this->calculoAhorro(Huffman::$mensaje);
            //Creacion del string del mensaje codificado
            $this->mensajeCodificado = $this->separarBitsLetra();
            //Determinacion de saltos '</br>' segun tamaño del mensaje
            $this->saltos = "</br>";
            $this->saltos .= $this->saltosBr(sizeOf(Huffman::$tablaFinal[0]));
        }

        //Funcion que devuleve el char de un string en x posicion
        private function charAt($mensaje,$i){
            return $mensaje[$i];
        }

        //Funcion que arma un array en funcion de las palabras no repetidas
        private static function frecuencia($letra){
            Huffman::$existe = false;
            for($i=0;$i<sizeof(Huffman::$subCaracteres);$i++){
                if(Huffman::$subCaracteres[$i] == $letra){
                    Huffman::$existe = true;
                    break;
                }
            }
            if(!Huffman::$existe){
                array_push(Huffman::$subCaracteres,$letra);
            }
        }

        //Funcion que ordena el array de caracteres no repetidos
        private static function ordenar($subCaracteres){
            sort($subCaracteres);
            return $subCaracteres;
        }

        //Funcion que devuelve el tamaño en x de la matriz
        private static function inicializar($subCaracteres){
            Huffman::$letras = array();
            $tamaño = sizeof($subCaracteres);
            return ($tamaño*2)-1;
        }

        //Funcion que determina el tamaño de la matriz letras
        private static function crearMatriz($tamaño){
            for($i=0;$i<7;$i++){
                for($j=0;$j<$tamaño;$j++){
                    Huffman::$letras[$i][$j] = null;
                }
            }
        }

        //Funcion que llena la matriz
        private static function llenar($fin){
            //llenar letras ordenadas
            for($i=0;$i<sizeOf(Huffman::$subCaracteres);$i++){
                Huffman::$letras[0][$i] = Huffman::$subCaracteres[$i];
            }
            //llenar frecuencia de letras
            for($i=0;$i<sizeOf(Huffman::$subCaracteres);$i++){
                $cont = strLen(Huffman::$mensaje) - strLen(str_replace(Huffman::$subCaracteres[$i],"",Huffman::$mensaje));
                Huffman::$letras[1][$i] = $cont;
            }
            //Lleno de no para hacer validaciones despues
            for($i=0;$i<$fin;$i++){
                Huffman::$letras[6][$i] = "No";
            }
        }

        //Funcion que completa la matriz
        private static function completar(){
            $fin = (sizeof(Huffman::$subCaracteres)*2 - 1) - sizeof(Huffman::$subCaracteres);
            for($i=0;$i<$fin;$i++){
                Huffman::sumatoria($i);
            }
        }

        //Funcion para encontrar padre hijo izq o der
        private static function sumatoria($aumento){
            $pos = Huffman::saberMenor($aumento);
            $pos2 = Huffman::saberMenor($aumento);
            $num1 = 0; $num2=0;
            for($i=0;$i<sizeof(Huffman::$subCaracteres)+$aumento;$i++){
                if($pos == $i){
                    $num1 = Huffman::$letras[1][$i];
                } 
                if($pos2 == $i){
                    $num2 = Huffman::$letras[1][$i];
                }
            }
            Huffman::$letras[1][sizeOf(Huffman::$subCaracteres) + $aumento] = $num1 + $num2;
            Huffman::$letras[4][sizeOf(Huffman::$subCaracteres) + $aumento] = $pos;
            Huffman::$letras[5][sizeOf(Huffman::$subCaracteres) + $aumento] = $pos2;

            Huffman::$letras[2][$pos] = sizeOf(Huffman::$subCaracteres) + $aumento;
            Huffman::$letras[3][$pos] = 1;

            Huffman::$letras[2][$pos2] = sizeOf(Huffman::$subCaracteres) + $aumento;
            Huffman::$letras[3][$pos2] = 2;
        }

        //Funcion para saber menor  
        private static function saberMenor($aumento){
            $menor = 10000;
            $pos1 = 0;
            for($i=0;$i<sizeof(Huffman::$subCaracteres)+$aumento;$i++){
                if(Huffman::$letras[1][$i] < $menor && Huffman::$letras[6][$i] == "No"){
                    $menor = Huffman::$letras[1][$i];
                    $pos1 = $i;
                }
            }
            Huffman::$letras[6][$pos1] = "Ya";
            return $pos1;
        }

        //Funcion para saber los bits del mensaje
        public function crearOrden($posLetra,$pos,$nivel,$col,$codigo){
            if($posLetra === "!"){
                array_push($this->dataOrden,Huffman::$letras[1][sizeOf(Huffman::$letras[0])-1]."-"."1"."-"."1"."-"."!"."-"." ");
                if(Huffman::$letras[4][sizeOf(Huffman::$letras[0]) - 1] !== null){
                   $this->crearOrden(Huffman::$letras[4][sizeOf(Huffman::$letras[0]) - 1],0, $nivel + 1,1,"0");
                }
                if(Huffman::$letras[5][sizeOf(Huffman::$letras[0]) - 1] !== null){
                    $this->crearOrden(Huffman::$letras[5][sizeOf(Huffman::$letras[0]) - 1],1, $nivel + 1,2,"1");
                }
            }else{
                $dir = intval($posLetra);
                if(Huffman::$letras[4][$dir] === null || Huffman::$letras[5][$dir] === null){
                    array_push($this->dataOrden,Huffman::$letras[0][$dir]."-".$pos."-".$nivel."-".$col."-"." ");
                    Huffman::$letras[6][$dir] = $codigo;
                }else{
                    array_push($this->dataOrden,Huffman::$letras[1][$dir]."-".$pos."-".$nivel."-".$col."-"." ");
                    if(Huffman::$letras[4][$dir] !== null){
                        $this->crearOrden(Huffman::$letras[4][$dir],0, $nivel + 1,($col*2)-1,$codigo.'0');
                    }
                    if(Huffman::$letras[5][$dir] !== null){
                        $this->crearOrden(Huffman::$letras[5][$dir],1, $nivel + 1,($col*2),$codigo.'1');
                    }
                }
            }
        }

        //Funcion para completar espacios la tabla
        public function actualizarTabla($tamaño){
            for($i=0;$i<7;$i++){
                for($j=0;$j<$tamaño;$j++){
                    if($i==0 && Huffman::$letras[$i][$j]===null){
                        Huffman::$letras[$i][$j] = $j;
                    }else if(Huffman::$letras[$i][$j]===null){
                        if($j==$tamaño-1 && $i==3){
                            Huffman::$letras[$i][$j] = "R";
                        }else{
                            Huffman::$letras[$i][$j] = "-";
                        }
                    }
                }
            }
        }

        //Funcion para finalizar tabla a mostrar al usuario
        private static function terminarTabla($tamaño){
            Huffman::$tablaFinal = array();
            for($i=0;$i<7;$i++){
                for($j=0;$j<$tamaño + 1;$j++){
                    if($i==0 ){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = null;
                        }else{
                            Huffman::$tablaFinal[$i][$j] = $j-1;
                        }
                    }else if($i==1){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = "S&iacute;mbolo";
                        }else{
                            Huffman::$tablaFinal[$i][$j] = Huffman::$letras[$i-1][$j-1];
                        }
                    }else if($i==2){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = "Frecuencia";
                        }else{
                            Huffman::$tablaFinal[$i][$j] = Huffman::$letras[$i-1][$j-1];
                        }
                    }else if($i==3){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = "Padre";
                        }else{
                            Huffman::$tablaFinal[$i][$j] = Huffman::$letras[$i-1][$j-1];
                        }
                    }else if($i==4){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = "Tipo";
                        }else{
                            Huffman::$tablaFinal[$i][$j] = Huffman::$letras[$i-1][$j-1];
                        }
                    }else if($i==5){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = "Izquierda";
                        }else{
                            Huffman::$tablaFinal[$i][$j] = Huffman::$letras[$i-1][$j-1];
                        }
                    }else if($i==6){
                        if($j==0){
                            Huffman::$tablaFinal[$i][$j] = "Derecha";
                        }else{
                            Huffman::$tablaFinal[$i][$j] = Huffman::$letras[$i-1][$j-1];
                        }
                    }
                }
            }
            //para impresion HTML
            Huffman::$tabla = "";
            for ($i = 0; $i < 7; $i++) {
                Huffman::$tabla .= "<tr>";
                    for ($j = 0; $j < $tamaño + 1; $j++) {
                        if ($i == 0) {
                            Huffman::$tabla .= "<th><h3><big>".Huffman::$tablaFinal[$i][$j]."</big></h3></th>";
                        } else if ($j == 0) {
                            Huffman::$tabla .= "<th><h3><big>".Huffman::$tablaFinal[$i][$j]."</big></h3></th>";
                        } else {
                            Huffman::$tabla .= "<td><big>".Huffman::$tablaFinal[$i][$j]."</big></td>";
                        }
                    }
                    Huffman::$tabla .= "</tr>";
            }
        }

        //Funcion que calcula el ahorro y muestra informacion de la codificacion del mensaje
        private function calculoAhorro($mensaje){
            $this->bitsInicial = strlen($mensaje)*8;
            $bitsFinal = $this->cantidadBits($mensaje);
            $append = "<i>Comprimido</i>: ".$bitsFinal." bits.</br>";
            $append .= '<p style="color:#007bff;"><i>Codificado</i>: '.$this->bitsCodificados($this->bitsAhorrados($this->bitsComprimido($this->bitsInicial,$bitsFinal),$this->bitsInicial))."%.</p>";
            $append .= '<p style="color:#04da36;"><i>Ahorrado</i>: '.$this->bitsAhorrados($this->bitsComprimido($this->bitsInicial,$bitsFinal),$this->bitsInicial)."%.</p>";
            $append2 = "<i>Bits iniciales</i>: ".strlen(Huffman::$mensaje). " (caracteres del mensaje) * 8 (ASCII) = ".intval($this->bitsInicial)." bits.</br>";
            return $append2.$append;
        }

        //funcion que calcula los bits comprimidos (iniciales - comprimidos)
        private function bitsComprimido($a,$b){
            $this->comprimido = intval($a-$b);
            return $this->comprimido;
        }

        //Funcion que calcula los bits ahorrados (comprimidos * 100)/iniciales redondeado a 3 decimas
        private function bitsAhorrados($a,$b){
            $this->ahorrado = $this->redondear((($a*100)/$b),3);
            return $this->ahorrado;
        }

        //Funcion que calcula los bits codificados (100 - % ahorrado) redondeado a 3 decimas
        private function bitsCodificados($a){
            $this->codificado = $this->redondear(100 - $a,3);
            return $this->codificado;
        }

        //Funcion que redondea decimales
        private function redondear($numero,$decimales){
            $factor = pow(10,$decimales);
            return (round($numero*$factor)/$factor);
        }

        //Funcion que determina la cantidad de bits del mensaje
        private function cantidadBits($mensaje){
            $this->codigo = "";
            $this->codigoLetra = "";
            for($i=0;$i<strlen($mensaje);$i++){
                $c = $this->charAt($mensaje,$i);
                for($j=0;$j<sizeOf(Huffman::$subCaracteres);$j++){
                    if($c==Huffman::$letras[0][$j]){
                        $this->codigo = $this->codigo . Huffman::$letras[6][$j];
                        $this->codigoLetra = $this->codigoLetra . Huffman::$letras[0][$j];
                        for($k=0;$k<strLen(Huffman::$letras[6][$j]);$k++){
                            $this->codigoLetra = $this->codigoLetra . " "; 
                        }
                    }
                }
            }
            return strlen($this->codigo);
        }

        //Funcion que vuelve presentable el valor en bits de cada letra del mensaje
        private function separarBitsLetra(){
            $this->mostrarDatosSep();
            $arreglo = array("");
            return $this->codigoLetra."</br>".$this->codigo."</br>".$this->datosSeparados."</br>".$this->datosSeparados2;
        }

        //Funcion que bifurca las bits y letras en string
        private function mostrarDatosSep(){
            for($i=0;$i<sizeOf(Huffman::$subCaracteres);$i++){
                if($i > sizeOf(Huffman::$subCaracteres)/2){
                    $this->datosSeparados2 = $this->datosSeparados2 .Huffman::$letras[0][$i]." = ".Huffman::$letras[6][$i]." ";
                }else{
                    $this->datosSeparados = $this->datosSeparados .Huffman::$letras[0][$i]." = ".Huffman::$letras[6][$i]." ";
                }
            }
        }

        //Funcion que hace el arbol HTML
        private function realizarArbol($cabezaArbol){
            $arbol= '';
            if($cabezaArbol === null){
                return '<li><span>*</span></li>';
            }else{
                $izquierda = $this->realizarArbol(Huffman::$letras[4][$cabezaArbol]);
                $derecha = $this->realizarArbol(Huffman::$letras[5][$cabezaArbol]);
                
                $arbol = '<li>' .
                '<div ' . (Huffman::$letras[0][$cabezaArbol] === null ? 'style="font-size: 0.9rem"' : '') . '><a><big><big><big><big><b>' .
                (Huffman::$letras[0][$cabezaArbol] !== null ? (Huffman::$letras[0][$cabezaArbol]) === 32 ? '&nbsp' : Huffman::$letras[0][$cabezaArbol] : Huffman::$letras[1][$cabezaArbol]) .
                '</b></big></big></big></big></a></div>';

                if(Huffman::$letras[4][$cabezaArbol] !== null ||Huffman::$letras[5][$cabezaArbol] !== null ){
                    $arbol .= '<ul>'.$izquierda.$derecha.'</ul>';
                }
                $arbol .= '</li>';
            }
            return $arbol;
        }

        //Funcion que determina los saltos '</br> en funcion del tamaño del mensaje'
        //Con el objetivo de no superponer elementos HTML
        private function saltosBr($tamaño){
            // tamaño con 43 se superpone, y apartir de este numero, cada 2 en tamaño
            if($tamaño > 43){
                $this->saltos .= "</br></br></br>";
                if($tamaño - 43 > 0 && $tamaño+1%2==0){
                    $this->saltos .= $this->saltosBr($tamaño);
                }
            }
            return $this->saltos;
        }

        //Funcion para imprimir
        private static function imprimir(){
            for($x=0;$x<sizeOf(Huffman::$letras);$x++){
                echo "</br>";
                for($y=0;$y<sizeOf(Huffman::$letras[$x]);$y++){
                    echo Huffman::$letras[$x][$y];
                }
            }
        }

        //Getters
        public function getTablaFinal(){
            return Huffman::$tabla;
        }

        public function getBitsDatos(){
            return Huffman::$bitsDatos;
        }
        
        public function getMensajeCodificado(){
            return $this->mensajeCodificado;
        }

        public function getTamaño(){
            return $this->inicializar(Huffman::$subCaracteres);
        }

        public function getArbol(){
            return $this->arbol;
        }

        public function getAhorrado(){
            return $this->ahorrado;
        }

        public function getCodificado(){
            return $this->codificado;
        }

        public function getSaltos(){
            return $this->saltos;
        }

        public function getGraficoBarra(){
            $html = '<ul class="chart1"><li>
                        <span style="height:'.$this->codificado.'%" title="Codificado">'.$this->codificado.'%</span>
                        </li><ul class="chart2"><li>
                        <span style="height:'.$this->ahorrado.'%" title="Ahorrado">'.$this->ahorrado.'%</span>
                        </li></ul></ul>';
            return $html;
        }
    }

    $tablaFinal;
    $bitsDatos;
    $mensajeCodificado;
    $tamaño;
    $arbol;
    $ahorrado;
    $angAhorrado;
    $codificado;
    $andCodificado;
    $saltos;
    $graficoBarra;
    //Declaracion variables POST y validacion
    if(!empty($_POST['mensaje'])){
        $mensaje = $_POST['mensaje'];
        $p = new Huffman($mensaje);
        $p->iniciar();
        $tablaFinal = $p->getTablaFinal();
        $bitsDatos =$p->getBitsDatos();
        $mensajeCodificado = $p->getMensajeCodificado();
        $tamaño = $p->getTamaño();
        $arbol = $p->getArbol();
        $codificado = $p->getCodificado();
        $ahorrado = $p->getAhorrado();
        $saltos = $p->getSaltos();
        $graficoBarra = $p->getGraficoBarra();
    }else{
        $mensaje = "Porfavor escriba un mensaje para codificar";
    }
    
    require'../views/index.view.php'
?>