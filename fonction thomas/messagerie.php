<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);



 function dbConnect(){
    $host = 'localhost';
    $dbname = 'sae3';
    $user = 'root';
    $password = '';

    $bdd = new PDO('mysql:host='.$host.';dbname='.$dbname,$user,$password);
    return $bdd;
 }

function afficheContacts($id_user){
    $bdd = dbConnect();
    $query = $bdd->query(('CALL listeContact('.$id_user.');'));;
    $contacts = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach($contacts as $contact){
        echo($contact['Prenom_Uti'].' '.$contact['Nom_Uti'].'</br>');
    }
}


afficheContacts(2);


?>
