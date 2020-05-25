# php-scrap-projetos-pesquisa-sigaa
Script criado para gerar planilha com dados de projetos de pesquisa através de arquivos html salvos no PC 

## Arquivos

São apenas dois arquivos. **composer.json** onde contém as bibliotecas usadas e  **index.php** que executa o script.

## Passo a passo

Para gerar as planilhas é necessário que siga os seguintes passos:

 - Acesse o módulo de pesquisa no Sigaa
 - Acesse a página https://sig.[instituticao].edu.br/sigaa/pesquisa/projetoPesquisa/buscarProjetos.do
 - Faça a busca pelo edital
 - Através do ícone de lupa(visualizar arquivo) abra a página do projeto
 - Salve todos os projetos (Ctrl+s) no formato html, em uma só pasta local
 - Execute esse script através do comando>
 `php index.php --pasta local-da-pasta`
 - Esse script criará uma planilha na raiz desse projeto
 
 ## Observações
 Esse script foi testado usando como base arquivos html gerados através do módulo de pesquisa da Unilab. É provável que em outra instituição haja a necessidade de alguma adaptação. O código é livre pra qualquer alteração e distribuição.
 
