<?php

function pspice_to_kicad_convert_test_file()
{

    $upload_root_path = pspice_to_kicad_upload_path();
    $convert_root_path = pspice_to_kicad_convert_path();
    $uploadfolder = arg(3);
    $uploadfile = arg(4);
    $filenamearray = explode('.', $uploadfile);
    $filename = $filenamearray[0];
    $filePath = $upload_root_path . "" . $uploadfolder . "/" . $uploadfile;
    _convert_run_script($convert_root_path, $uploadfolder, $filePath, $filename, $uploadfile);
}

function _convert_run_script($convert_root_path, $uploadfolder, $filePath, $filename, $uploadfile)
{
//var_dump($convert_root_path . " " . $uploadfolder . " " . $filePath . " " . $filename." ".$uploadfile);
    $sh_command = "./run.sh " . $convert_root_path . " " . $uploadfolder . " " . $filePath . " " . $filename . " " . $uploadfile;
    exec($sh_command);

}
