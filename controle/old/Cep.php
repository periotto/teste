<?php
/*  Paulo Sergio D. Adaptado para o WOlf CMS
 *  Função de busca de Endereço pelo CEP 
 *  -   Desenvolvido Felipe Olivaes para ajaxbox.com.br 
 *  -   Utilizando WebService de CEP da republicavirtual.com.br 
 * 
 *Exemplo de utilização:
 * $resultado_busca = new busca_cep('88036429');
   $resultado_busca->$endereco_Completo;
 *     
 *$resultado_busca['tipo_logradouro'] -> Rua. Avenida e etc. 
  $resultado_busca['logradouro']      -> Nome da rua
  $resultado_busca['bairro']          -> Bairro
  $resultado_busca['cidade']          -> Cidade
  $resultado_busca['uf']              -> UF
 */
 class busca_cep
 { // BEGIN class busca_cep
 	// variables
 	var $cep;
   public $endereco_Completo;
   
 	// constructor
 	function busca_cep($cep)
 	{ // BEGIN constructor
 		$this->busca_cep_Replubica($cep);
 	} // END constructor
  private function busca_cep_Replubica($cep){  
    $resultado = @file_get_contents('http://republicavirtual.com.br/web_cep.php?cep='.urlencode($cep).'&formato=query_string');  
    if(!$resultado){  
        $resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";  
    }  
    parse_str($resultado, $retorno);   
    
    $this->endereco_Completo = $retorno['tipo_logradouro']." ".$retorno['logradouro'].", ".$retorno['cidade']." - ".$retorno['uf'];                
    return $retorno;  
  }
 } // END class busca_cep    