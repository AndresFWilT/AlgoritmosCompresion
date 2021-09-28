<?php
class shannon_Fano
{
    //Metodos y variables

    //Mensaje
    private $mensaje;
    //Variables
    private $caracteres, $frecuencias, $entropia, $estadoRecorrido, $bitsCodigo,
        $bitsMsj, $shannonFano, $matriz, $entropiaMsj, $mnsjCod, $probabilidades, $matrizD;
    private $centinelaInicial = 0, $centinelaFinal = 0;
    //Variables vista
    private $tablaHTML, $saltos, $bitsInicial, $bitsFinal, $comprimido, $ahorrado, $codificado,
        $mensajeCodificado;

    //Constructor
    function __construct($m)
    {
        $this->mensaje = $m;
        $this->crearEstadoRecorrido($m);
    }

    private function crearEstadoRecorrido($m)
    {
        $this->estadoRecorrido = array();
        $this->caracteres = array();
        $this->frecuencias = array();
        for ($i = 0; $i < strlen($m); $i++) {
            $this->estadoRecorrido[$i] = false;
            $this->caracteres[$i] = "";
            $this->frecuencias[$i] = 0;
        }
    }

    //Metodo que inicializa el algoritmo Shanon-Fano
    public function iniciar()
    {
        //1->Determinar la frecuencia de aparicion
        $this->determinarFrecuencia();
        //2->Ordenar de menor a mayor la frecuencia de aparicion
        $this->reduce_recursivo($this->centinelaInicial, $this->centinelaFinal);
        //3->Arreglo que almacena el codigo Shannon-Fano
        $this->crearShannonFano();
        //4->se realiza la codificacion
        $this->calcularCodShannonFano($this->centinelaInicial, $this->centinelaFinal);
        //5->Calculo de la entropia y bits mensaje
        $this->crearTablaDatos();
        $this->crearTablaCod();
        //Realizar tabla para mostrarla al usuario
        $this->hacerTablaHTML(sizeOf($this->matriz[0]) + 5);
        //Para realizar la codificacion del mensaje
        $this->mensajeCodificado = $this->codificacionMsj();
        $this->armarMsjCod();
        $this->bitsDatos = $this->calculoAhorro($this->mensaje);
        //Determinacion de saltos '</br>' segun tamaño del mensaje
        $this->saltos = "</br>";
        $this->saltos .= $this->saltosBr(sizeOf($this->frecuencias));
    }

    //Funcion que determina la frecuencia de aparicion de cada caracter
    private function determinarFrecuencia()
    {
        for ($k = 0; $k < strlen($this->mensaje); $k++) {
            $frecuencia = 0;
            for ($j = $k; $j < strlen($this->mensaje); $j++) {
                if ($this->charAt($this->mensaje, $k) === ($this->charAt($this->mensaje, $j)) && !$this->estadoRecorrido[$j]) {
                    $frecuencia++;
                    $this->estadoRecorrido[$j] = true;
                }
            }
            if ($frecuencia != 0) {
                $this->caracteres[$this->centinelaFinal] = $this->charAt($this->mensaje, $k);
                $this->frecuencias[$this->centinelaFinal] = $frecuencia;
                $this->centinelaFinal++;
            }
        }
        $this->centinelaFinal--;
    }

    //Permite ordenar las frecuencias de aparicion de forma descendente
    //Ordenamiento QuickSort
    private function reduce_recursivo($pIni, $pFin)
    {
        $izq = $pIni;
        $der = $pFin;
        $pos = $pIni;
        $aux = "";
        $band = true;
        while ($band) {
            $band = !$band;
            //Comparacion del primero de la izq con todos los que se encuentra de der a izq
            while (($this->frecuencias[$pos] >= $this->frecuencias[$der]) && ($pos != $der)) {
                //Recorre de der a izq
                $der = $der - 1;
                //Realiza intercambios posicion incorrecta
            }
            if ($pos != $der) {
                //Intercambio de datos
                $aux = $this->frecuencias[$pos];
                $strAux = $this->caracteres[$pos];

                $this->frecuencias[$pos] = $this->frecuencias[$der];
                $this->caracteres[$pos] = $this->caracteres[$der];

                $this->frecuencias[$der] = $aux;
                $this->caracteres[$der] = $strAux;

                //Der posicion intercambiada nuevo pivote
                $pos = $der;
                //A partir de la posicion pivote, va a compara de izq a der con el elemento en el
                //Pivote
                while (($this->frecuencias[$pos] <= $this->frecuencias[$izq]) && ($pos != $izq)) {
                    $izq = $izq + 1;
                }
                //Comparacion para realizar intercambio a una pos menor
                if ($pos != $izq) {
                    $band = true;
                    //Intercambio de datos
                    $aux = $this->frecuencias[$pos];
                    $strAux = $this->caracteres[$pos];

                    $this->frecuencias[$pos] = $this->frecuencias[$izq];
                    $this->caracteres[$pos] = $this->caracteres[$izq];

                    $this->frecuencias[$izq] = $aux;
                    $this->caracteres[$izq] = $strAux;
                    //Se inicializa el nuevo pivote
                    $pos = $izq;
                }
            }
        }
        if ($pos - 1 > $pIni) {
            $this->reduce_recursivo($pIni, $pos - 1);
        }
        if ($pFin > $pos + 1) {
            $this->reduce_recursivo($pos + 1, $pFin);
        }
    }

    private function crearShannonFano()
    {
        $this->shannonFano = array();
        for ($i = 0; $i < $this->centinelaFinal + 1; $i++) {
            $this->shannonFano[$i] = "";
        }
    }

    private function calcularCodShannonFano($cInicial, $cFinal)
    {
        $puntoMedio = $this->centinelaIntermedio($cInicial, $cFinal);
        $this->sfCalcularParteSuperior($cInicial, $puntoMedio);
        $this->sfCalcularParteInferior($puntoMedio + 1, $cFinal);
    }

    //apartir de la frecuencia total se calcula la posicion media
    private function centinelaIntermedio($centinelaIni, $centinelaFin)
    {
        $frecuenciaTotal = 0;
        $frecuenciaMedia = 0;
        $centienlaMediano = $centinelaIni;
        //Determina la frecuencia a partir de un intervalo dado
        for ($k = $centinelaIni; $k <= $centinelaFin; $k++) {
            $frecuenciaTotal += $this->frecuencias[$k];
        }
        //Permite determinar el centinela que se encuentra en la frec media
        while (($frecuenciaMedia <= ($frecuenciaTotal / 2)) && ($centienlaMediano < $centinelaFin)) {
            $frecuenciaMedia = $this->frecuencias[$centienlaMediano] + $frecuenciaMedia;
            $centienlaMediano++;
        }
        return $centienlaMediano - 1;
    }

    //Tabla imaginaria superior "Asignacion del 0"
    private function sfCalcularParteSuperior($cInicial, $cMedio)
    {
        if (($cInicial === $cMedio) || ($cMedio <= 0)) {
            $this->shannonFano[$cInicial] = $this->shannonFano[$cInicial] . "0";
        } else {
            for ($j = $cInicial; $j <= $cMedio; $j++) {
                $this->shannonFano[$j] = $this->shannonFano[$j] . "0";
            }
            $this->calcularCodShannonFano($cInicial, $cMedio);
        }
    }

    //Tabla imaginaria superior "Asignacion del 1"
    private function sfCalcularParteInferior($cInicial, $cFinal)
    {
        if ($cInicial >= $cFinal) {
            $this->shannonFano[$cInicial] = $this->shannonFano[$cInicial] . "1";
        } else {
            for ($j = $cInicial; $j <= $cFinal; $j++) {
                $this->shannonFano[$j] = $this->shannonFano[$j] . "1";
            }
            $this->calcularCodShannonFano($cInicial, $cFinal);
        }
    }

    //Tabla de informacion de entropia y bits
    private function crearTablaDatos()
    {
        //Se calcula la probabilidad
        $this->calcularProbabilidad();
        //Se calcula la entropia y entr del msj
        $this->calcularEntropia();
        //Se calculan los bits codigo de cada caracter y bits Msj
        $this->calcularBitsCod();
    }

    //Funcion que calcula la probabilidad
    private function calcularProbabilidad()
    {
        $this->probabilidades = array();
        $frecT = $this->sumatoria($this->frecuencias);
        for ($i = 0; $i < sizeof($this->frecuencias); $i++) {
            $this->probabilidades[$i] = $this->redondear($this->frecuencias[$i] / $frecT, 3);
        }
    }

    //Funcion que calcula la entropia
    private function calcularEntropia()
    {
        $this->entropia = array();
        $this->entropiaMsj = array();
        for ($i = 0; $i < sizeof($this->shannonFano); $i++) {
            $this->entropia[$i] = abs($this->redondear(log($this->probabilidades[$i], 2), 3));
            $this->entropiaMsj[$i] = $this->redondear($this->entropia[$i] * $this->frecuencias[$i], 3);
        }
    }

    //Funcion que calcula los bits cod
    private function calcularBitsCod()
    {
        $this->bitsCodigo = array();
        $this->bitsMsj = array();
        for ($i = 0; $i < sizeof($this->shannonFano); $i++) {
            $this->bitsCodigo[$i] = strlen($this->shannonFano[$i]);
            $this->bitsMsj[$i] = $this->frecuencias[$i] * $this->bitsCodigo[$i];
        }
    }

    //Tabla de codificacion y pasos
    private function crearTablaCod()
    {
        $cadenaCaracteres = "";
        $encabezado = "";
        $col = 0;
        $fil = 0;
        $i = 0;
        $j = 0;
        //Se determina el # de columnas
        for ($fil = 0; $fil < sizeOf($this->shannonFano); $fil++) {
            if ($col < strlen($this->shannonFano[$fil])) {
                $col = strlen($this->shannonFano[$fil]);
            }
        }
        $col += 1;
        $this->crearMatriz($fil + 1, $col + 1);
        //Construccion del encabezado
        $this->matriz[0][0] = "S&iacute;mbolo";
        $this->matriz[0][$col] = "Codificaci&oacute;n";
        for ($i = 1; $i < $col; $i++) {
            $this->matriz[0][$i] = "Etapa " . $i;
        }
        //Construccion de la columna 0 y n
        for ($i = 1; $i <= $fil; $i++) {
            $this->matriz[$i][0] = $this->caracteres[$i - 1];
            $this->matriz[$i][$col] = $this->shannonFano[$i - 1];
        }

        //Insercion de los caracteres individuales
        for ($i = 1; $i <= $fil; $i++) {
            for ($j = 1; $j <= strlen($this->shannonFano[$i - 1]); $j++) {
                $this->matriz[$i][$j] = "" . $this->charAt($this->shannonFano[$i - 1], $j - 1);
            }
        }
    }

    private function crearMatriz($f, $c)
    {
        $this->matriz = array();
        for ($i = 0; $i < $f; $i++) {
            for ($j = 0; $j < $c; $j++) {
                $this->matriz[$i][$j] = "";
            }
        }
    }


    //Funcion que devuleve el char de un string en x posicion
    private function charAt($mensaje, $i)
    {
        return $mensaje[$i];
    }

    //Funcion que redondea decimales
    private function redondear($numero, $decimales)
    {
        $factor = pow(10, $decimales);
        return (round($numero * $factor) / $factor);
    }

    //Funcion para calcular sumatorias de algo
    private function sumatoria($arreglo)
    {
        $suma = 0;
        for ($i = 0; $i < sizeOf($arreglo); $i++) {
            $suma += $arreglo[$i];
        }
        return $suma;
    }

    //Funcion para finalizar tabla a mostrar al usuario
    private function hacerTablaHTML($tamaño)
    {
        $this->tablaFinal = array();
        for ($i = 0; $i < sizeOf($this->shannonFano) + 2; $i++) {
            for ($j = 0; $j <= $tamaño; $j++) {
                if ($i == 0) {
                    if ($j == 0) {
                        $this->tablaFinal[$i][$j] = "S&iacute;mbolo";
                    } else if ($j == 1) {
                        $this->tablaFinal[$i][$j] = "Frecuenc&iacute;a";
                    } else if ($j == 2) {
                        $this->tablaFinal[$i][$j] = "Probabilidad";
                    } else if ($j == 3) {
                        $this->tablaFinal[$i][$j] = "Entrop&iacute;a";
                    } else if ($j == 4) {
                        $this->tablaFinal[$i][$j] = "Entrop&iacute;a msj";
                    } else if ($j == 5) {
                        $this->tablaFinal[$i][$j] = "Bits c&oacute;digo";
                    } else if ($j == 6) {
                        $this->tablaFinal[$i][$j] = "Bits mensaje";
                    } else {
                        $this->tablaFinal[$i][$j] = $this->matriz[0][$j - 6];
                    }
                } else if ($i != 0 && $i < sizeOf($this->shannonFano) + 1) {
                    if ($i < sizeof($this->shannonFano) + 1) {
                        if ($j == 0) {
                            $this->tablaFinal[$i][$j] = $this->caracteres[$i - 1];
                        } else if ($j == 1) {
                            $this->tablaFinal[$i][$j] = $this->frecuencias[$i - 1];
                        } else if ($j == 2) {
                            $this->tablaFinal[$i][$j] = $this->probabilidades[$i - 1];
                        } else if ($j == 3) {
                            $this->tablaFinal[$i][$j] = $this->entropia[$i - 1];
                        } else if ($j == 4) {
                            $this->tablaFinal[$i][$j] = $this->entropiaMsj[$i - 1];
                        } else if ($j == 5) {
                            $this->tablaFinal[$i][$j] = $this->bitsCodigo[$i - 1];
                        } else if ($j == 6) {
                            $this->tablaFinal[$i][$j] = $this->bitsMsj[$i - 1];
                        } else {
                            $this->tablaFinal[$i][$j] = $this->matriz[$i][$j - 6];
                        }
                    } else {
                        $this->tablaFinal[$i][$j] = "";
                    }
                } else {
                    if ($j == 0) {
                        $this->tablaFinal[$i][$j] = "TOTAL";
                    } else if ($j == 1) {
                        $this->tablaFinal[$i][$j] = $this->sumatoria($this->frecuencias);
                    } else if ($j == 2) {
                        $this->tablaFinal[$i][$j] = $this->redondear($this->sumatoria($this->probabilidades), 2);
                    } else if ($j == 3) {
                        $this->tablaFinal[$i][$j] = $this->sumatoria($this->entropia);
                    } else if ($j == 4) {
                        $this->tablaFinal[$i][$j] = $this->sumatoria($this->entropiaMsj);
                    } else if ($j == 5) {
                        $this->tablaFinal[$i][$j] = $this->sumatoria($this->bitsCodigo);
                    } else if ($j == 6) {
                        $this->tablaFinal[$i][$j] = $this->sumatoria($this->bitsMsj);
                    } else {
                        $this->tablaFinal[$i][$j] = "";
                    }
                }
            }
        }
        //para impresion HTML
        $this->tablaHTML = "";
        for ($i = 0; $i < sizeOf($this->shannonFano) + 2; $i++) {
            $this->tablaHTML .= "<tr>";
            for ($j = 0; $j <= $tamaño; $j++) {
                if ($i == 0) {
                    $this->tablaHTML .= "<th><h3><big>" . $this->tablaFinal[$i][$j] . "</big></h3></th>";
                } else if ($i == sizeOf($this->shannonFano) + 1) {
                    $this->tablaHTML .= "<th><h3><big>" . $this->tablaFinal[$i][$j] . "</big></h3></th>";
                } else {
                    $this->tablaHTML .= "<td><big>" . $this->tablaFinal[$i][$j] . "</big></td>";
                }
            }
            $this->tablaHTML .= "</tr>";
        }
    }

    //Funcion que calcula lo ahorrado
    //Funcion que calcula el ahorro y muestra informacion de la codificacion del mensaje
    private function calculoAhorro($mensaje)
    {
        $this->bitsInicial = strlen($mensaje) * 8;
        $bitsFinal = strlen($this->mnsjCod);
        $append = "<i>Comprimido</i>: " . $bitsFinal . " bits.</br>";
        $append .= '<p style="color:#007bff;"><i>Codificado</i>: ' . $this->bitsCodificados($this->bitsAhorrados($this->bitsComprimido($this->bitsInicial, $bitsFinal), $this->bitsInicial)) . "%.</p>";
        $append .= '<p style="color:#04da36;"><i>Ahorrado</i>: ' . $this->bitsAhorrados($this->bitsComprimido($this->bitsInicial, $bitsFinal), $this->bitsInicial) . "%.</p>";
        $append2 = "<i>Bits iniciales</i>: " . strlen($this->mensaje) . " (caracteres del mensaje) * 8 (ASCII) = " . intval($this->bitsInicial) . " bits.</br>";
        return $append2 . $append;
    }

    //funcion que calcula los bits comprimidos
    private function bitsComprimido($a, $b)
    {
        $this->comprimido = intval($a - $b);
        return $this->comprimido;
    }

    //Funcion que calcula los bits ahorrados
    private function bitsAhorrados($a, $b)
    {
        $this->ahorrado = $this->redondear((($a * 100) / $b), 3);
        return $this->ahorrado;
    }

    //Funcion que calcula los bits codificados
    private function bitsCodificados($a)
    {
        $this->codificado = $this->redondear(100 - $a, 3);
        return $this->codificado;
    }

    private function codificacionMsj()
    {
        $this->mnsjCod = "";
        $estado = true;
        for ($j = 0; $j < strlen($this->mensaje); $j++) {
            for ($i = 0; ($i < $this->centinelaFinal + 1) && $estado; $i++) {
                if ($this->charAt($this->mensaje, $j) === $this->caracteres[$i]) {
                    $estado != $estado;
                    $this->mnsjCod = $this->mnsjCod . $this->shannonFano[$i];
                }
            }
            $estado != $estado;
        }
        return $this->mnsjCod;
    }

    private function armarMsjCod(){
        $cadena = $this->mensajeCodificado;
        $size = sizeOf($this->matriz[0])-1;
        $cadenaIni = "";
        for($i = 0;$i < sizeOf($this->shannonFano);$i++){
            if($i === sizeOf($this->shannonFano)-1){
                $cadenaIni .= $this->caracteres[$i] . " = " . $this->matriz[$i+1][$size];
            }else{
                $cadenaIni .= $this->caracteres[$i] . " = " . $this->matriz[$i+1][$size] .", ";
            }
            
        }
        $this->mensajeCodificado = $cadenaIni . "</br>".$cadena ."</br>" . '<p style="color:#800080;">Mensaje original</p>'.$this->mensaje;
    }

    //Funcion que determina los saltos '</br> en funcion del tamaño del mensaje'
    //Con el objetivo de no superponer elementos HTML
    private function saltosBr($tamaño)
    {
        // tamaño con 7 se superpone, y apartir de este numero, cada 1 en tamaño un br
        if ($tamaño > 7) {
            for ($i = 7; $i < $tamaño; $i++) {
                if ($i % 4 == 0) {
                    $this->saltos .= "</br>";
                }
            }
        }
        return $this->saltos;
    }

    //Funcion para imprimir arreglo
    private function imprimirArreglo($tabla)
    {
        for ($x = 0; $x < sizeOf($tabla); $x++) {
            echo $tabla[$x];
            echo "</br>";
        }
    }

    //Funcion para imprimir matriz
    private function imprimirMatriz($tabla)
    {
        for ($x = 0; $x < sizeOf($tabla); $x++) {
            echo "</br>";
            for ($y = 0; $y < sizeOf($tabla[$x]); $y++) {
                echo $tabla[$x][$y] . "&nbsp;";
            }
        }
    }

    //Getters

    public function getTablaHTML()
    {
        return $this->tablaHTML;
    }

    public function getSaltos()
    {
        return $this->saltos;
    }

    public function getBitsDatos()
    {
        return $this->bitsDatos;
    }

    public function getGraficoBarra()
    {
        $html = '<ul class="chart1"><li>
                        <span style="height:' . $this->codificado . '%" title="Codificado">' . $this->codificado . '%</span>
                        </li><ul class="chart2"><li>
                        <span style="height:' . $this->ahorrado . '%" title="Ahorrado">' . $this->ahorrado . '%</span>
                        </li></ul></ul>';
        return $html;
    }

    public function getAhorrado()
    {
        return $this->ahorrado;
    }

    public function getCodificado()
    {
        return $this->codificado;
    }

    public function getMesjCod()
    {
        return $this->mensajeCodificado;
    }
}

$tabla;
$saltos;
$bitsDatos;
$graficoBarra;
$codificado;
$ahorrado;
$saltosA;
$mensajeCodificado;
//Declaracion variables POST y validacion
if (!empty($_POST['mensaje'])) {
    $mensaje = $_POST['mensaje'];
    $p = new shannon_Fano($mensaje);
    $p->iniciar();
    $tabla = $p->getTablaHTML();
    $saltos = $p->getSaltos();
    $bitsDatos = $p->getBitsDatos();
    $graficoBarra = $p->getGraficoBarra();
    $codificado = $p->getCodificado();
    $ahorrado = $p->getAhorrado();
    $saltosA = $saltos . $saltos . $saltos;
    $mensajeCodificado = $p->getMesjCod();
} else {
    $mensaje = "Porvafor escriba un mensaje para codificar";
}

require '../views/shannonFano.view.php';
