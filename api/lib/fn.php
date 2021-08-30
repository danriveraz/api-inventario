<?php

  function show( $mensaje )
  {
    echo "<pre>";
    print_r( $mensaje );
    echo "</pre>";
  }

  function limpiarNumero($dato) { // Antes llamada filter
    $dato = str_replace(array("\\", "º", "-", "#", "|", "!", "\"", "·", "$", "%", "&",
        "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "´", "]", "+", "}", "{", "´", ">", "<", ";", ",", ":", ".", " "),
        '', $dato );
    return $dato;
  }


  function limpiarSimbolos($dato) { // Antes llamada filter
    $dato = str_replace(array("\\", "º", "-", "#", "|", "!", "\"", "·", "$", "%", "&",
        "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "´", "]", "+", "}", "{", "´", ">", "<", ";", ",", ":"),
        '', $dato );
    return $dato;
  }
?>
