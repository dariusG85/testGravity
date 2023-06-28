<?php
function security($data)                          //funkcja wycinająca spacje zawnętrzne, ukośniki i znaczniki html, dla bezpieczeństwa wprowadzania danych
 {
 $data = trim($data);
 $data = stripslashes($data);
 $data = strip_tags($data);
 return($data);
 }
?>