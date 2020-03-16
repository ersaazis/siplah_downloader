<?php
function download($id,$page){
    $url="https://siplah.blibli.com/backend/products?itemPerPage=50&page=$page&sortField=undefined&sortDirection=undefined&merchantId=$id";
    $json=file_get_contents($url);
    $rawData=json_decode($json,true);
    $data=$rawData['data'];
    $output="";

    $numfile=$page*50;
    $i=$numfile+1;
    foreach($data as $item){
        $imageFileName="";
        foreach($item['images'] as $num=>$image){
            $imageFileName.=", foto_{$i}_{$num}.jpg";
            $downImage = fopen($id."/foto_{$i}_{$num}.jpg", "w") or die("Unable to open file!");
            fwrite($downImage, file_get_contents($image['url']));
            fclose($downImage);
            $i++;
        }
        $output.="$item[name];\"$item[description]\";$item[price];$imageFileName \n";
    }
    return $output;
}

echo "Input ID : ";
$id=trim(fgets(STDIN));
$url="https://siplah.blibli.com/backend/products?itemPerPage=50&page=0&sortField=undefined&sortDirection=undefined&merchantId=$id";
$json=file_get_contents($url);
$rawData=json_decode($json,true);
$data=$rawData['data'];
$page=$rawData['paging']['total_page'];

mkdir($id);

$output="Nama;Deskripsi;Harga;Foto \n";
for ($i=0; $i <= $page ; $i++) { 
    $output.=download($id,$i);
}

$myfile = fopen("$id.csv", "w") or die("Unable to open file!");
fwrite($myfile, $output);
fclose($myfile);

$zip = new ZipArchive();
$zip->open("$id.zip", ZipArchive::CREATE);
$zip->addFile("$id.csv");
$options = array('add_path' => "$id/", 'remove_all_path' => TRUE);
$zip->addGlob("$id/*", 0, $options);
$zip->close();
