#!/bin/bash

#Log file location
log="/tmp/psicekicad.log"
cmd_path1="/home/prashant/www/html/drupal/drupal_7.x/r_fossee_in/sites/all/modules/custom/pspice_to_kicad/"
cmd_path2="eSim_PSpice_to_KiCad_Python_Parser/lib/PythonLib/"
parser_command="parser.py "

echo "###########Start Conversion from PSPICE to KICAD#################" >> $log
echo "" >>$log
echo "The Conversion starts at `date`" >> $log
####Getting Parameter
convertedSchematic=$1
filepath=$2
username=$3
cwd=`pwd`
echo $filepath
echo $convertedSchematic

echo "">>$log
echo "The paramters to the script is : ">>$log
echo "File : $filepath">>$log
echo "Username : $username">>$log
filename=`basename $filepath`
filewithoutExt="${filename%.*}"

echo "File name is : $filename">>$log
echo "File name without extension : $filewithoutExt">>$log
echo "">>$log

#Create Directory for every User

if [ -d $convertedSchematic ];then
    echo "User directory $convertedSchematic is already available">>$log
else
    mkdir -p $convertedSchematic
fi

echo "The converted file will be present at $convertedSchematic">>$log

#Creating directory for uploaded Project
mkdir -p $convertedSchematic/$filewithoutExt

#Converting PSpice to Kicad Schematic
echo "Calling Schematic conversion script" >>$log
python3.7 $cmd_path1$cmd_path2$parser_command $filepath $convertedSchematic/$filewithoutExt

#Converting to Zip file
cd $convertedSchematic
#sudo zip -rq -rm $zipname $filewithoutExt
echo "Creating zip file of converted project">>$log
zip -r $filewithoutExt{.zip,} 2>&1>>$log
echo "The zip file is present at `pwd`">>$log
cd $cwd
rm -rf $convertedSchematic/$filewithoutExt

echo "###########End PSICE to KICAD Conversion#########################">>$log
echo " ">>$log

exit

