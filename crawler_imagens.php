<?php
/*
 * Criado em  2015 - 05/02/2015
 * Autor: Alexandre Muniz <alexandre.muniz@vieiramuniz.com.br>
 * O objetivo desse script é obter imagens da url fornecida, salvar no diretório especificado 
 * e atualizar uma tabela em um banco MYSQL com os caminhos das imagens
 */
$dados_acesso_banco  = array(
 							'host'=>'localhost',// host de acesso ao banco
 							'username'=>'user',// usuario do banco
 							'password'=>'test123',// senha do banco
 							'database'=>'sample'// nome do banco
 						);
 						
# como o script pode demorar, dizemos ao interpretador para ignorar o tempo máximo e executar o script até o fim
set_time_limit(0);
 
 $app_path = '/Framework/static/projeto/uploads/oldsite';// aqui voce coloca onde quer que as imagens sejam salvas
 $folder_image_path = $_SERVER['DOCUMENT_ROOT'].$app_path;
 
 $script_saida_sql = 'query.sql';
 						
# Realizando a conexão						
if(!mysql_connect($dados_acesso_banco['host'],$dados_acesso_banco['username'],$dados_acesso_banco['password']))
 	exit('<strong>Falha ao conectar no host</strong>');						

if(!mysql_select_db($dados_acesso_banco['database']))
 	exit('<strong>Falha ao selecionar o banco '.$dados_acesso_banco['database'].'</strong>');						
 	
# Criando a pasta ( se não existir, ele cria!!)	
if(!is_dir($folder_image_path))
 	mkdir($folder_image_path,0777,true);
 	
$sql = "SELECT id, texto FROM post ";

$res = mysql_query(stripslashes($sql));

if(!$res)
 	exit('<strong>Falha ao executar a consulta: '.$sql);						

while($row=mysql_fetch_array($res))
{
	$conteudo = $row['texto'];
	$id = $row['id'];
	$doc = new DOMDocument();
	
	@$doc->loadHTML($conteudo);
	
	$tags = $doc->getElementsByTagName('img');
	
	foreach($tags as $tag)
	{
		$image_path = $tag->getAttribute('src');
		$image_name = end(explode('/gc/',$image_path));
		$file_name = end(explode('/',$image_name));
		
		$dir_path = str_replace('/'.$file_name,'',$image_name);
		
		if(!is_dir($folder_image_path.'/'.$dir_path))
			mkdir($folder_image_path.'/'.$dir_path,0777,true);
			
		$final_path = $folder_image_path.'/'.$image_name;
		
		echo 'copiando de <strong>'.$image_path.'</strong> para <strong>'.$final_path.'</strong><br >';
		
		file_put_contents($final_path, file_get_contents($image_path));
		
	}
	
}
