<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no,initial-scale=1.0,maximum-scale=1.0">
    <title>Info Shannon-Fano</title>
    <!--css-->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,300" rel='stylesheet' type='text/css'">
    <link rel=" stylesheet" href="../css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/bootstrap-4.0.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <!--Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
</head>

<body>
    <div class="contenedor-Titulo">
        <h1 class="titulo">Codificaci&oacute;n Shannon-Fano</h1>
    </div>
    </br>
    <div id="info">
        <a href="../views/shannonFano.view.php">Aplicacion Codificac&iacute;on Shannon-Fano</a>
    </div>
    </br></br></br>
    <div id="contenido">
        <h2>¿Qu&eacute; es el m&eacute;todo de Shannon?</h2></br>
        <p>Es un metodo, que se refiere a la probabilidad de aparici&oacute;n de cada s&iacute;mbolo en un mensaje,
            b&aacute;sicamente se utiliza para la compresi&oacute;n de datos.</br>
            Este m&eacute;todo de codificaci&oacute;n fue desarrollado por Claude Shannon en laboratorios Bell y por
            Robert Fano en el MIT en la d&eacute;cada de los 40 casi</br> simult&aacute;neamente. La t&eacute;cnica fue
            propuesta por Claude Elwood Shannon, en "Una Teor&iacute;a matematica de la comunicaci&oacute;n", su art&iacute;culo
            de 1948 </br> introduciendo el campo de la teor&iacute;a del a informac&iacute;on. Este m&eacute;todo fue atribuido a Robert Fano
            ,quien posteriormente lo public&oacute; como un informe t&eacute;cnico.
        </p></br>
        <h2>Propiedades de la tabla de c&oacute;digos</h2></br>
        <p>c&oacute;digos, diferentes de estos tienen diferentes tipos de bits.</p></br>
        <ul>
            <li>
                <p>Los c&oacute;digos para s&iacute;mbolos con bajas probabilidades tienen m&aacute;s bits.</p>
            </li>
            <li>
                <p>Los c&oacute;digos para s&iacute;mbolos con altas probabilidades tienen menos bits.</p>
            </li>
            <li>
                <p>Los c&oacute;digos de longitud diferente pueden ser un&iacute;vocamente decodificados.</p>
            </li>
        </ul></br>
        <h2>¿Qu&eacute; es la entrop&iacute;a?</h2></br>
        <p>Para nuestros fines, la entrop&iacute;a se refiere a la cantidad de bits necesarios para representar un s&iacute;mbolo.</br>
        <ul>
            <li>
                La entrop&iacute;a en un s&iacute;mbolo = - log<sub>2</sub> (X), donde X = la probabilidad de cada caracter.
            </li>
            <li>
                La entrop&iacute;a en un mensaje = suma de la entrop&iacute;a de sus s&iacute;mbolos
            </li>
        </ul>
        </p></br>
    </div>
    <caption id="caption">Para mas informaci&oacute;n visitar: <a href="http://esimioscu.blogspot.com/2015/10/codificacion-shannon-fano.html?m=1" style="color: white;" target="blank">Shannon</a></caption>
</body>

</html>