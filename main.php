<?php 
include 'functions.php';                                                  //podpięcie pliku z funkcją bezpieczeństwa (functions.php)
$name = '';                                                               //zdefiniowanie zmiennych, na których będą wykonywane operacje
$username = '';
$email = '';
$address = '';
$phone = '';
$company = '';
$name_error = '';                                                         //zdefiniowanie zmiennych, przeznaczonych do błędów
$username_error = '';
$email_error = '';
$address_error = '';
$phone_error = '';
$company_error = '';
$read = file_get_contents("users.json");                                  //odczytywanie zawartości z pliku json
if(empty($read))                                                          //sprawdzanie czy zawartość pliku json nie jest całkowicie pusta
 {
 $read = '[]';
 }
$table = json_decode($read,true);                                         //przypisanie zawartości pliku json do tablicy
if(isset($_POST['submit']))                                               //sprawdzanie czy submit wprowadzający nowe dane, został naciśnięty
 {
 $name = security($_POST['name']);                                        //przypisywanie do zmiennych zawartości tablicy $_POST, razem z funkcją bezpieczeństwa
 $username = security($_POST['username']);
 $email = security($_POST['email']);
 $address = security($_POST['address']);
 $phone = security($_POST['phone']);
 $company = security($_POST['company']);
 if(empty($name))                                                         //sprawdzanie czy pola formularzy nie są puste i czy są prawidłowe np. username musi mieć
  {                                                                       //co najmniej 4 znaki i musi być unikatowy, email musi mieć poprawny format itd.
  $name_error = 'The name field is empty!';
  }
 if(empty($username)) 
  {
  $username_error = 'The username field is empty!';
  }
  elseif(strlen($username) < 4)
   {
   $username_error = 'The username is to short!';
   }
   else
    {
    for($counter=0 ; $counter<count($table) ; $counter++)
     {
     if($username==$table[$counter]['username'])
      {
      $username_error = 'The username already exists!';
      }
     }
    }
 if(empty($email))
  {
  $email_error = 'The email field is empty!';
  }
  elseif(!(filter_var($email,FILTER_VALIDATE_EMAIL)))
   {
   $email_error = 'The email has invalid format!';
   }
 if(empty($address))
  {
  $address_error = 'The address field is empty!';
  }
 if(empty($phone))
  {
  $phone_error = 'The phone field is empty!';
  }
 if(empty($company))
  {
  $company_error = 'The company field is empty!';
  }
 if($name_error == null && $username_error == null && $email_error == null && $address_error == null && $phone_error == null && $company_error == null)       //sprawdzanie czy nie ma żadnych błędów podczas wypełniania formularza
  {
  $table_id = [];
  for($counter=0 ; $counter<count($table) ; $counter++)                                    //sprawdzanie i wyszukiwanie największego id z istniejących danych. Jeśli danych nie ma, to id będzie miało wartość 1
   {
   $table_id[] = $table[$counter]['id'];
   }
  if($table == [])
   {
   $id_help = 1;
   }
  else
   {
   $id_help = max($table_id) + 1;
   }
  $table[]=['id' => $id_help , 'name' => $name, 'username' => $username, 'email' => $email, 'address' => $address, 'phone' => $phone, 'company' => $company];   //wprowadzanie nowych danych (z odpowiednim id) z formularza do pliku json
  $table=json_encode($table);
  $fopen=fopen('users.json','w');
  fwrite($fopen,$table);
  fclose($fopen);
  header("Location: main.php");                                                            //odświeżenie strony głównej main
  }
 }
?>    
<!DOCTYPE html>                                                                 
<html>                                                                     
 <head>
  <meta charset="UTF-8">
  <title>Backend/Full-stack recruitment task</title> 
  <link rel="stylesheet" href="styles.css">                                                                  
 </head>                                                                   
 <body>
  <main>                                                                    
   <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">                      <!-- ustawienie metody formularza na post, oraz wysyłania danych z formularza do własnego pliku (PHP_SELF) -->
    <?php
    if($read != '[]')                                                                     //sprawdzanie czy są dane do wyświetlenia
     {
     ?>
     <table>
      <thead>
       <tr>
       <?php
        foreach ($table[0] as $key => $value)                                             //wyświetlenie nagłówka tabeli (bez wyświetlania id)
         {
         if(!($key === array_key_first($table[0])))
          {
          echo '<th>'.strtoupper($key).'</th>';
          }
         }
       ?>
       <th></th>
       </tr>
      </thead>
      <tbody>
       <?php
       for($counter=0 ; $counter<count($table) ; $counter++)                              //wyświetlenie całej zawartości tabeli (bez wyświetlania id)
        {
        echo '<tr>';
        foreach ($table[$counter] as $key => $value)
         {
         if(!($key === array_key_first($table[$counter])))
          {
          echo '<td>'.$value.'</td>';
          }
         }
        echo '<td><a href="delete.php?id='.$table[$counter]['id'].'"><input type="button" value="REMOVE"></a></td>';                      //wprowadzenie do każdego wiersza tabeli klawisza usuwającego jako odsyłacz
        echo '</tr>';                                                                                                                     //do innej strony, na której będzie się odbywało usuwanie danego wiersza (delete.php)
        }
       ?>
      </tbody>
     </table>
      <?php
       }
       else                           
        {
        echo '<h1><img src="exclam.png" alt="exclam" class="left">No data!<img src="exclam.png" alt="exclam" class="right"></h1>';        //wyświetlenie komunikatu o braku danych do wyświetlenia
        }
      ?>
    <hr>
    <h3>Every field is required!</h3>
    <div>
     <label for="name">Name</label>                                                                                                       <!-- wprowadzenie formularzu do wypełnienia, w celu dodania nowego wiersza -->
     <input type="text" id="name" name="name" placeholder="no extra requirements" value="<?php echo $name; ?>">                           <!-- do istniejącej już tabeli. Dodatkowo wypełnione dane w polach będą -->
     <label for="username">Username</label>                                                                                               <!-- zapamiętane, jeśli w innych polach pojawią się błędy -->
     <input type="text" id="username" name="username" placeholder="min 4 characters, unique" value="<?php echo $username; ?>">
     <label for="email">Email</label>
     <input type="text" id="email" name="email" placeholder="email format" value="<?php echo $email; ?>">
     <label for="address">Address</label>
     <input type="text" id="address" name="address" placeholder="no extra requirements" value="<?php echo $address; ?>">
     <label for="phone">Phone</label>
     <input type="text" id="phone" name="phone" placeholder="no extra requirements" value="<?php echo $phone; ?>">
     <label for="company">Company</label>
     <input type="text" id="company" name="company" placeholder="no extra requirements" value="<?php echo $company; ?>">
     <input type="submit" id="submit" name="submit" value="SUBMIT">
     <?php
     if($name_error != null)                                                               //wyświetlanie odpowiednich komunikatów w razie pojawieniu się błędów podczas wypełniania formularza
      {
      echo '<p>'.$name_error.'</p>';
      }
     if($username_error != null)
      {
      echo '<p>'.$username_error.'</p>';
      }
     if($email_error != null)
      {
      echo '<p>'.$email_error.'</p>';
      }
     if($address_error != null)
      {
      echo '<p>'.$address_error.'</p>';
      }
     if($phone_error != null)
      {
      echo '<p>'.$phone_error.'</p>';
      }
     if($company_error != null)
      {
      echo '<p>'.$company_error.'</p>';
      }
     ?>
    </div>
   </form>
  </main>                                                                  
 </body>                                                                   
</html>
