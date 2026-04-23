<?php
function download_literature_survey_report()
{
	$submission_id = arg(3);
	$root_path = hackathon_submission_files_path();
    $dir_path = $root_path . "latex/";
    //var_dump($dir_path);die;
    $submission_circuit_filedata = "";
    $reference_filedata = "";
    $eol = "\n";
    $sep = "#";
    $submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey} WHERE id = :id", array(
        ':id' => $submission_id
    ));
    $submission_data = $submission_q->fetchObject();
    if (!$submission_data)
    {
        \Drupal::messenger()->addError('Invalid submission specified.');
        drupal_goto('');
    } //!$preference_data
    $submission_circuit_filedata = $submission_data->circuit_name . $sep . $submission_data->participant_name . $sep . $submission_data->institute . $sep . $submission_data->abstract . $sep . $submission_data->circuit_details . $eol;
    /* check if book already generated */
    //var_dump(file_exists($dir_path . "book_" . $submission_data->id . ".pdf"));die;
    if (file_exists($dir_path . $submission_data->id . "_literature_survey_report.pdf"))
    {
        // download PDF file 
        unlink($dir_path . $submission_data->id . "_literature_survey_report.pdf");
    }//file_exists($dir_path . "book_" . $preference_data->id . ".pdf")
    $circuit_diagram_q  = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey_files} WHERE submission_id = :id and filetype = :filetype", array(
        ':id' => $submission_data->id,
        ':filetype' => 'C'
    ));
    $circuit_diagram_path = $root_path . $circuit_diagram_q->fetchObject()->filepath;
    $waveform_q  = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey_files} WHERE submission_id = :id and filetype = :filetype", array(
        ':id' => $submission_data->id,
        ':filetype' => 'W'
    ));
    $waveform_path = $root_path . $waveform_q->fetchObject()->filepath;

    $bib_references_q  = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey_bib_references} WHERE submission_id = :id", array(
        ':id' => $submission_data->id,
    ));
        /*while ($submission_files_data = $submission_files_q->fetchObject())
        {
                $reference_filedata .= $submission_files_data->filename . $sep;
                $reference_filedata .= $submission_files_data->filepath . $sep;
                $reference_filedata .= $submission_files_data->filetype . $sep;
                $reference_filedata .= $sep;
                $reference_filedata .= $submission_files_data->id;
                $reference_filedata .= $eol;
        }*/ //$example_data = $example_q->fetchObject()
    /********************* Write to tex file ***********************/
    $bibscript = '';
    while($bib_ref_data = $bib_references_q->fetchObject()){
        $resource_link = hs_convert_special_characters($bib_ref_data->resource_link);
    $bibscript .= '@MISC{' . $bib_ref_data->id . ', author={' . hs_convert_special_characters($bib_ref_data->resource_author) . '}, title={' . hs_convert_special_characters($bib_ref_data->resource_title) . '}, howpublished={' . $resource_link . '}}' . $eol;
}

$circuit_details = hs_convert_special_characters($submission_data->circuit_details);
$abstract = hs_convert_special_characters($submission_data->abstract);
$circuit_name = hs_convert_special_characters($submission_data->circuit_name);
$participant_name = hs_convert_special_characters($submission_data->participant_name);
$participant_institute = hs_convert_special_characters($submission_data->institute);

$bib_fn="references.bib";
$bib_file = fopen($dir_path . $bib_fn, "w");
fwrite($bib_file, $bibscript);
fclose($bib_file);


    $texscript = '
\documentclass[10pt,twocolumn,letterpaper]{article}
%%
%   Template taken from Overleaf
%   Fill in details where prompted
%
%% Language and font encodings
\usepackage[english]{babel}
\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
%\usepackage{url}
\usepackage{amssymb}
\usepackage{mathptmx}
\usepackage[mathletters]{ucs}
%% Sets page size and margins
\usepackage[a4paper,top=1cm,bottom=2cm,left=2cm,right=2cm,marginparwidth=1.75cm]{geometry}
\usepackage{float}
%% Useful packages
\usepackage{amsmath}
\usepackage{graphicx}
%\usepackage[backend=bibtex]{biblatex}
%\usepackage{ieee}
%\usepackage[colorinlistoftodos]{todonotes}
%\usepackage[colorlinks=true, allcolors=blue]{hyperref}
%\usepackage{underscore}
\usepackage{textcomp}

\title{ ' . $circuit_name . ' }

\usepackage{authblk}
\author[1]{' . $participant_name . ', ' . $participant_institute . '}

\begin{document}
\maketitle

\selectlanguage{english}
\begin{abstract}
' . $abstract . '
\end{abstract}


\section{Reference Circuit Details}

' . $circuit_details . '

\section{Reference Circuit}
\begin{figure}[H]
\centering
\includegraphics[width=0.4\textwidth]{'. $circuit_diagram_path .'}
\caption{\label{fig:RefCktDiagram} Reference circuit diagram.}
%~\cite{001}}
\end{figure}

\section{Reference Circuit Waveforms}
\begin{figure}[H]
\centering
\includegraphics[width=0.4\textwidth]{' . $waveform_path . '}
\caption{\label{fig:RefWaveform}Reference waveform.}
%~\cite{6556063}}
\end{figure}

\nocite{*}

\bibliographystyle{ieee}
%\addbibresource{references.bib}
\bibliography{references}

\end{document}';

//write code to file
$fn=$submission_data->id . "_literature_survey_report.tex";
$myfile = fopen($dir_path . $fn, "w");
fwrite($myfile, $texscript);
fclose($myfile);

chdir("esim_uploads/hackathon_submission_uploads");
chdir("latex");
$ref_fn = $submission_data->id . "_literature_survey_report";

$sh_command = "/bin/bash pdf_creator.sh " . $fn . " " . $ref_fn;
exec($sh_command);
$download_filename = $submission_data->id . "_literature_survey_report.pdf";

if (filesize($dir_path . $submission_data->id . "_literature_survey_report.pdf") == TRUE)
        {
            ob_clean();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Type: application/pdf');
            header('Content-disposition: attachment; filename=' . $download_filename);
            header('Content-Length: ' . filesize($dir_path . $submission_data->id . "_literature_survey_report.pdf"));
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: no-cache');
            @readfile($dir_path . $submission_data->id . "_literature_survey_report.pdf");
            ob_end_flush();
            ob_clean();
            flush();
        } //filesize($dir_path . $pdf_filename) == TRUE
        else
        {
            \Drupal::messenger()->addError("Error occurred when generating the PDF version of the report.");
            drupal_goto('');
        }
    
}

function download_final_submission_report()
{
    $final_submission_id = arg(3);
    $root_path = hackathon_submission_files_path();
    $dir_path = $root_path . "latex/";
    //var_dump($dir_path);die;
    $final_submission_circuit_filedata = "";
    $reference_filedata = "";
    $eol = "\n";
    $sep = "#";
    $final_submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission} WHERE id = :id", array(
        ':id' => $final_submission_id
    ));
    $final_submission_data = $final_submission_q->fetchObject();
    $submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey} WHERE id = :id", array(
        ':id' => $final_submission_data->literature_survey_id
    ));
    $submission_data = $submission_q->fetchObject();
    if (!$final_submission_data)
    {
        \Drupal::messenger()->addError('Invalid submission specified.');
        drupal_goto('');
    } //!$preference_data
    //$final_submission_circuit_filedata = $submission_data->circuit_name . $sep . $final_submission_data->participant_name . $sep . $final_submission_data->institute . $sep . $final_submission_data->abstract . $sep . $final_submission_data->circuit_details . $eol;
    /* check if book already generated */
    //var_dump(file_exists($dir_path . "book_" . $final_submission_data->id . ".pdf"));die;
    if (file_exists($dir_path . $submission_data->id . "_final_submission_report.pdf"))
    {
        // download PDF file 
        unlink($dir_path . $submission_data->id . "_final_submission_report.pdf");
    }//file_exists($dir_path . "book_" . $preference_data->id . ".pdf")
    $circuit_diagram_q  = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission_files} WHERE final_submission_id = :id and filetype = :filetype", array(
        ':id' => $final_submission_data->id,
        ':filetype' => 'C'
    ));
    $circuit_diagram_path = $root_path . $circuit_diagram_q->fetchObject()->filepath;
    $waveform_q  = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission_files} WHERE final_submission_id = :id and filetype = :filetype", array(
        ':id' => $final_submission_data->id,
        ':filetype' => 'W'
    ));
    $waveform_path = $root_path . $waveform_q->fetchObject()->filepath;

    $bib_references_q  = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission_bib_references} WHERE final_submission_id = :id", array(
        ':id' => $final_submission_data->id,
    ));
        /*while ($final_submission_files_data = $final_submission_files_q->fetchObject())
        {
                $reference_filedata .= $final_submission_files_data->filename . $sep;
                $reference_filedata .= $final_submission_files_data->filepath . $sep;
                $reference_filedata .= $final_submission_files_data->filetype . $sep;
                $reference_filedata .= $sep;
                $reference_filedata .= $final_submission_files_data->id;
                $reference_filedata .= $eol;
        }*/ //$example_data = $example_q->fetchObject()
    /********************* Write to tex file ***********************/
    $bibscript = '';
    while($bib_ref_data = $bib_references_q->fetchObject()){
        $resource_link = hs_convert_special_characters($bib_ref_data->resource_link);
    $bibscript .= '@MISC{' . $bib_ref_data->id . ', author={' . hs_convert_special_characters($bib_ref_data->resource_author) . '}, title={' . hs_convert_special_characters($bib_ref_data->resource_title) . '}, howpublished={' . $resource_link . '}}' . $eol;
}

$circuit_details = hs_convert_special_characters($final_submission_data->circuit_details);
$abstract = hs_convert_special_characters($final_submission_data->abstract);
$circuit_name = hs_convert_special_characters($submission_data->circuit_name);
$participant_name = hs_convert_special_characters($submission_data->participant_name);
$participant_institute = hs_convert_special_characters($submission_data->institute);

$bib_fn="references.bib";
$bib_file = fopen($dir_path . $bib_fn, "w");
fwrite($bib_file, $bibscript);
fclose($bib_file);


    $texscript = '
\documentclass[10pt,twocolumn,letterpaper]{article}
%%
%   Template taken from Overleaf
%   Fill in details where prompted
%
%% Language and font encodings
\usepackage[english]{babel}
\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
%\usepackage{url}
\usepackage{amssymb}
\usepackage{mathptmx}
\usepackage[mathletters]{ucs}
%% Sets page size and margins
\usepackage[a4paper,top=1cm,bottom=2cm,left=2cm,right=2cm,marginparwidth=1.75cm]{geometry}
\usepackage{float}
%% Useful packages
\usepackage{amsmath}
\usepackage{graphicx}
%\usepackage[backend=bibtex]{biblatex}
%\usepackage{ieee}
%\usepackage[colorinlistoftodos]{todonotes}
%\usepackage[colorlinks=true, allcolors=blue]{hyperref}
%\usepackage{underscore}
\usepackage{textcomp}

\title{ ' . $circuit_name . ' }

\usepackage{authblk}
\author[1]{' . $participant_name . ', ' . $participant_institute . '}

\begin{document}
\maketitle

\selectlanguage{english}
\begin{abstract}
' . $abstract . '
\end{abstract}


\section{Circuit Details}

' . $circuit_details . '

\section{Implemented Circuit}
\begin{figure}[H]
\centering
\includegraphics[width=0.4\textwidth]{'. $circuit_diagram_path .'}
\caption{\label{fig:RefCktDiagram} Implemented circuit diagram.}
%~\cite{001}}
\end{figure}

\section{Implemented Waveforms}
\begin{figure}[H]
\centering
\includegraphics[width=0.4\textwidth]{' . $waveform_path . '}
\caption{\label{fig:RefWaveform}Implemented waveform.}
%~\cite{6556063}}
\end{figure}

\nocite{*}

\bibliographystyle{ieee}
%\addbibresource{references.bib}
\bibliography{references}

\end{document}';

//write code to file
$fn=$submission_data->id . "_final_submission_report.tex";
$myfile = fopen($dir_path . $fn, "w");
fwrite($myfile, $texscript);
fclose($myfile);

chdir("esim_uploads/hackathon_submission_uploads");
chdir("latex");
$ref_fn = $submission_data->id . "_final_submission_report";

$sh_command = "/bin/bash pdf_creator.sh " . $fn . " " . $ref_fn;
exec($sh_command);
$download_filename = $submission_data->id . "_final_submission_report.pdf";

if (filesize($dir_path . $submission_data->id . "_final_submission_report.pdf") == TRUE)
        {
            ob_clean();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Type: application/pdf');
            header('Content-disposition: attachment; filename=' . $download_filename);
            header('Content-Length: ' . filesize($dir_path . $submission_data->id . "_final_submission_report.pdf"));
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: no-cache');
            @readfile($dir_path . $submission_data->id . "_final_submission_report.pdf");
            ob_end_flush();
            ob_clean();
            flush();
        } //filesize($dir_path . $pdf_filename) == TRUE
        else
        {
            \Drupal::messenger()->addError("Error occurred when generating the PDF version of the report.");
            drupal_goto('');
        }
    
}