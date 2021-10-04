<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/fpdf/fpdf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field

    $studentEmail = $_POST['email'];
    $studentName = $_POST['studentName'];
    $studentNumber = $_POST['studentNumber'];
    $lessonCode = $_POST['lessonCode'];
    $date = $_POST['date'];
    $type = $_POST['type'];
    $fileType = $_POST['fileType'];

    $target_dir = "updoadmolhem/";
    $fileName = $target_dir . $studentNumber."-".$lessonCode."-".$type."-".$date.".pdf";


    if ($_POST['fileType'] == 'pdf'){
        $target_file = $target_dir . basename($_FILES["pdfFile"]["name"]);
        savePdf($target_file , $_FILES , $fileName);
    }else{
       saveImages($_FILES , $fileName);
    }
}

function savePdf($target_file  , $file  , $fileName){
    $pdfFile = $file['pdfFile'];
    $uploadOk = 1;

//    echo '<br> *******<br>';
//    var_dump($target_file);
//    echo '<br> ******* <br>';

    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Allow certain file formats
    if($imageFileType != "pdf") {
        echo "Sorry, pdf files are allowed.";
        $uploadOk = 0;
    }

// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {

        if (move_uploaded_file($pdfFile["tmp_name"], $fileName)) {
            echo "The file ". htmlspecialchars( basename( $pdfFile["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

function saveImages($files , $fileName){
    $target_dir = "updoadmolhem/";
    array_splice($files , 0 , 1); // remove pdf file request
    $uploadOk = 1;

    $fpdf = new FPDF();

    foreach ($files as $file){
        echo '<br>';
        var_dump($file['tmp_name']);
        if (!empty($file['name'])) {
            $target_file = $target_dir . basename($file["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
                return false;
            }else{
                $imageArray = explode('.' , $file['name']);
                $imageSuffix = end($imageArray);
                $time = $_SERVER['REQUEST_TIME'];
                $imageAddress = './images/'.$time.rand(10000 , 99999).'.' . $imageSuffix;

                if (move_uploaded_file($file["tmp_name"], $imageAddress)) {
                    echo "The file ". htmlspecialchars( basename( $file["name"])). " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }

            $fpdf->AddPage();
            $fpdf->Image($imageAddress,10,10 , 150);
        }
    }
    var_dump($fileName);
    $fpdf->Output($fileName , 'f');

}