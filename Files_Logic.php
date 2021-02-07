<?php

    $conn = mysqli_connect("localhost","root","","verificacao_seguranca");
    //*
    $sql = "SELECT * FROM arquivos";
    $result = mysqli_query($conn,$sql);
    $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
    //*

    //UPLOAD LOGIC
    if(isset($_POST['enviar_arquivo'])) {

        $filename = $_FILES['arquivo']['name'];
        $destination = 'evidence_files/' . $filename;

        $formatosPermitidos = array("pdf","xlsx","docx","png");
        
        $extension = pathinfo($filename,PATHINFO_EXTENSION);

        $file = $_FILES['arquivo']['tmp_name'];
        $size = $_FILES['arquivo']['size'];

        if(!in_array($extension,$formatosPermitidos)){

            echo "Your file extension must be .pdf, .xlsx or .docx";

        }
        elseif($_FILES['arquivo']['size'] > 10000000){
            echo " File is too Large";
        }
        else{
            if(move_uploaded_file($file,$destination)){


                if(isset($_GET['evidence_id'])){

                    $evidence_id = $_GET['evidence_id']; 
                    $sql = "INSERT INTO arquivos (evidence_id,name,description,size) VALUES ('$evidence_id','$filename','implementar depois','$size')";
                    
                }
                
                if(mysqli_query($conn,$sql)){

                    echo "File Uploaded Successfully";
                }
                else{
                    echo "Failed to Upload File";
                }

                    

            }
        }
        
    }

//DOWNLOAD LOGIC

// if(isset($_GET['file_id'])){

//     $id = $_GET['file_id'];

//     $sql = "SELECT * FROM arquivos WHERE id=$id";

//     $result = mysqli_query($conn,$sql);

//     $file = mysqli_fetch_assoc($result);

//     $filepath = 'evidence_files/'.$file['name'];
  
//     if(file_exists($filepath) ){
        

//         // header('Content-Type: application/octet-stream');

//         // header('Content-Transfer-Encoding: binary');

//         // header ('Content-Description: File Transfer');

//         // header('Content-Disposition: attachment ; filename='.basename($filepath));

//         // header('Expires: 0');

//         // header(' Cache-Control: must-revalidate');

//         // header('Pragma:public');

//         // header('Content-Length:' . filesize('evidence_files/'.$file['name']));

//         // readfile('evidence_files/' . $file['name']);

//         // exit;
//     }
//     else{
//         echo "Failed to Download File";
//     }

// }



?>