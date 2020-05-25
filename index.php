<?php
//Para usar esse script siga os seguinte passos:
//1 - Acesse o módulo de pesquisa no sigaa
//2 - Acesse a página https://sig.unilab.edu.br/sigaa/pesquisa/projetoPesquisa/buscarProjetos.do
//3 - Faça a busca pelo edital 
//4 - Através do ícone de lupa(visualizar arquivo) abra a página do projeto
//5 - Salve todos os projetos (Ctrl+s) no formato html, em uma só pasta local
//6 - Execute esse script através do comando>php index.php --pasta local-da-pasta
//Esse script criará uma planilha na raiz desse projeto


$val = getopt(null, ["pasta:"]);

if(!isset($val['pasta'])){
	echo "Você deve passar o caminho da pasta a ser pesquisada.". PHP_EOL;
	echo "Use o comando --pasta caminho-para-a-pasta". PHP_EOL;
	die();
}else if(!is_dir($val['pasta'])){
	echo "A pasta informada [" . $val['pasta'] . "] não existe ou não é uma pasta de arquivos.". PHP_EOL;
	echo "Use o comando --pasta caminho-para-a-pasta". PHP_EOL;
	die();
}

$localPath = $val['pasta'];
$arquivos = scandir($val['pasta']);
require_once(__DIR__ . "/vendor/autoload.php");
use DiDom\Document;
$document = new Document();

foreach($arquivos as $a){
	
	if(strpos($a, ".html")){
		
		$dom = $document->loadHtml(file_get_contents_utf8($localPath . "/" . $a));
		$a = substr($a, 0, -5);
		$tabela = $dom->find('table.visualizacao')[0]->find('tbody tr td');
		$projeto[$a]['processo'] 	= substr(trim($tabela[0]->text()),0,-5);
		$projeto[$a]['titulo'] 		= trata($tabela[1]->text());
		$projeto[$a]['tipo'] 		= trata($tabela[2]->text());
		$projeto[$a]['categoria'] 	= trata($tabela[3]->text());
		$projeto[$a]['situacao'] 	= trata($tabela[4]->text());
		$projeto[$a]['unidade'] 	= trata($tabela[5]->text());
		$projeto[$a]['centro'] 		= trata($tabela[6]->text());
		$projeto[$a]['palavraChave'] = trata($tabela[7]->text());
		$projeto[$a]['email'] 		= trata($tabela[8]->text());
		$projeto[$a]['edital'] 		= trata($tabela[9]->text());
		$projeto[$a]['arquivoA'] 	= trata($tabela[11]->text());
		$projeto[$a]['arquivoB'] 	= trata($tabela[12]->text());
		$projeto[$a]['grandeArea'] 	= trata($tabela[14]->text());
		$projeto[$a]['area'] 		= trata($tabela[15]->text());
		$projeto[$a]['subarea'] 	= trata($tabela[16]->text());
		$projeto[$a]['grupoPesquisa'] = trata($tabela[19]->text());
		$projeto[$a]['resumo'] 		= trata($tabela[21]->text());
		$projeto[$a]['introducao'] 	= trata($tabela[22]->text());
		$projeto[$a]['objetivos'] 	= trata($tabela[23]->text());
		$projeto[$a]['metodologia'] = trata($tabela[24]->text());
		$projeto[$a]['referencias'] = trata($tabela[25]->text());

		$planos = $dom->find('table.subFormulario');
		foreach($planos as $k => $v)
			if($v->find('caption')[0]->text() == "Planos de Trabalho"){
				$projeto[$a]['planos'] = $v->count('tr');
			}else if($v->find('caption')[0]->text() == "Histórico do Projeto"){
				$submissao = $v->find('tr:last-child td');
				$projeto[$a]['horaSubmissao'] = trata($submissao[0]->text());
				$projeto[$a]['status'] = trata($submissao[1]->text());
				$projeto[$a]['responsavel'] = trata($submissao[2]->text());
			}

		//$projeto[$a]['planos']	= $dom->find('table.subFormulario tbody')[2]->count('tr');

		//$submissao = $dom->find('table.subFormulario tbody')[3]->find('tr:last-child td');
		

		$edital = $projeto[$a]['edital'];

		echo $projeto[$a]['processo'] . " - " . $a . " - " . $projeto[$a]['planos']  . " - " .
			 $projeto[$a]['horaSubmissao'] . " - " .  $projeto[$a]['status'] . " - " . $projeto[$a]['responsavel'] . PHP_EOL;
	};
}

$edital = explode(' - ', $edital);
$edital = 'Edital-' . slug($edital[0]);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

$i = 1;
foreach($projeto as $k => $v){
	if($i == 1){
		$x = 1;
		foreach($v as $p => $t)	$sheet = $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($x++, 1, $p);
		$i++;
	}
	$sheet = $spreadsheet->getActiveSheet()->fromArray($v, NULL, 'A'.$i++);
}
$writer = new Xlsx($spreadsheet);
$writer->save($edital .'.xlsx');
echo "O arquivo [" . $edital . ".xlsx] foi criado." . PHP_EOL;
function trata($texto)
{
	$texto = preg_replace('/\s+/', ' ', $texto);
	if(strpos($texto, "  "))
		trata(str_replace("  ", " ", $texto));
	return trim($texto);
}

function file_get_contents_utf8($fn) {
	$content = file_get_contents($fn);
	 return mb_convert_encoding($content, 'UTF-8',
		 mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function slug($str)
{
	$str = strtolower(trim($str));
	$str = preg_replace('/[^a-z0-9-]/', '-', $str);
	$str = preg_replace('/-+/', "-", $str);
	return $str;
}

