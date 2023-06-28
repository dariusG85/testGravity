<?php
if(isset($_GET['id']))                                           //sprawdzanie i pobieranie odpowiedniego id z głównego pliku main, za pomocą tablicy $_GET
 {
 $read = file_get_contents("users.json");                        //odczytywanie zawartości tablicy z pliku json
 $table = json_decode($read,true);
 $delete = $_GET['id'];
 for($counter=0 ; $counter<count($table) ; $counter++)           //pętla usuwająca daną pozycję z tablicy
  {
  if($table[$counter]['id']==$delete)
   {
   array_splice($table,$counter,1);
   }
  }
 $table=json_encode($table);                                     //zapisywanie zawartości tablicy do pliku json
 $fopen=fopen('users.json','w');                       
 fwrite($fopen,$table);
 fclose($fopen);
 header("Location: main.php");                                   //przejście do głównego pliku main
 }
?>