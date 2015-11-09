<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pruebas</title>  
    <link href="<?php echo BASEURL . 'resources/css/bootstrap.min.css'; ?>" rel="stylesheet">
    <link href="<?php echo BASEURL . 'resources/css/bootstrap-theme.min.css'; ?>" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo BASEURL . 'resources/js/bootstrap.min.js' ;?>"></script>
</head>
<body>    
    <div class="container">
        <?php echo $this->twig->render("components/bootstrap3/formulario/formulario.html.twig", array("config" => $configForm)); ?>
    </div>    
        
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo BASEURL . 'resources/js/bootstrap.min.js' ?>"></script>
</body>
</html>