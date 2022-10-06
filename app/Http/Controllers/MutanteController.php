<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MutanteController extends Controller
{
    private $a;
    private $b;
    public function __construct()
    {

        $this->a=0;
        $this->b=0;
        
    }


    public function mutant(Request $request){
      /*   $dna = ["ATGCGA",
                "CAGTGC",
                "TTATGT",
                "AGAAGG",
                "CCCCTA",
                "TCACTG"]; */
        //recibiendo json string
        $json = $request->input('dna', null);
        
        //convertiendo a array
        $dna = json_decode($json, true); 
        /* dd($dna); */
        $adnIsvalid= $this->adnIsvalid($dna);
        $data = array(
            'status' => 'error',
            'code' => 403,
            'message' => 'Forbidden'
        );

        $this->a=count($dna);
        if($adnIsvalid){
            
            $matriz=$this->convertArraytoMatriz($dna);
            
           $matriz2=$this->convertArraytoMatrizReverse($dna);
          
           
       
            if($this->isMutant($dna,$matriz,$matriz2)>1){
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'OK'
                );
            }
          
                 
        }


        return response()->json($data, $data['code']);

    }

    //validando si el adn es valido
    private function adnIsvalid($dna){
        //letras validas para el ADN
        $validate=["A","T","C","G"];
        //si tiene menos de cuatro items no es valido
        if(count($dna)<4){
            return false;
        }
        //cantidad de filas
        $this->a=count($dna);
        //recorriendo adn
        foreach($dna as $value){

            //valida que sea mayo o igual a 4 
            if(strlen($value)>=4){
                //cantidad de columnas
                $this->b=strlen($value);
                for($i=0; $i<strlen($value);$i++){
                    //comparando con lo valores validos
                    if(!in_array($value[$i],$validate)){
                        //no valido
                        return false;
                    }
                    
                }
                //si el tamaño no es n x n no es valida
                if($this->b!= $this->a){
                    return false;
                }
            }else{
                //novalido por la longitud
                return false;
            }
            
                
        }
        //si valido
        return true;

    }
    private function isMutant($dna,$matriz,$matriz2){
      
        $A1="AAAA";
        $T1="TTTT";
        $C1="CCCC";
        $G1="GGGG";
        $countSecuence=0;
        $columns=$this->convertColums($dna);
        $vertical=$this->convertVertical($matriz);
        $vertical2=$this->convertVertical($matriz2);
     /*    dd($matriz2,$vertical2); */
        //validando filas
        foreach($dna as $value){
            //validando filas
            if(str_contains($value, $A1) || str_contains($value, $T1) || str_contains($value, $C1) || str_contains($value, $G1)){
                ++$countSecuence;
            }

        }
        //validando columnas
        foreach($columns as $value){
            //validando columnas
            if(str_contains($value, $A1) || str_contains($value, $T1) || str_contains($value, $C1) || str_contains($value, $G1)){
                ++$countSecuence;
            }

        }

        //validando verticales
        foreach($vertical as $value){
            //validando verticales
            if(str_contains($value, $A1) || str_contains($value, $T1) || str_contains($value, $C1) || str_contains($value, $G1)){
                ++$countSecuence;
            }

        }
        //validando verticales
        foreach($vertical2 as $value){
            //validando verticales
            if(str_contains($value, $A1) || str_contains($value, $T1) || str_contains($value, $C1) || str_contains($value, $G1)){
                ++$countSecuence;
            }

        }


        return  $countSecuence;


    }

    private function convertColums($dna){
        $columns=array();  
        for($i=0; $i<$this->b;$i++){
            $string='';
            foreach($dna as $value){
               $string.=$value[$i];

              
               $columns[$i] = $string;
            }
        }
       return $columns;
        
    }

    private function convertArraytoMatriz($dna){
        $matriz=array();
        $j=0;
        foreach($dna as $value){
            
            for($i=0; $i<$this->a;$i++){
                $matriz[$j][$i]=$value[$i];
            }
            $j++; 
           
            
        }
        

        return $matriz;

    }
    private function convertArraytoMatrizReverse($dna){
        $reverse=array();
       
        $j=0;
        foreach($dna as $value){

            for($b=0,$i=$this->a-1; $i>-1;$i--,$b++){
                $reverse[$j][$b]=$value[$i];
            }
            $j++;
        }
        return $reverse;
        
    } 
   
    private function convertVertical($matriz){
        
        $vertical=array();
        
        for ($r = 0; $r < $this->a; $r++)
        {
            $string='';
            // comienza desde cada celda de la primera columna
            for ($i = $r, $j = 0; $j < $this->a && $i >= 0; $i--, $j++) {
              
                $string.=$matriz[$i][$j];
                
            }
            $vertical[]=$string;
            
            
        }
      
 
        // imprime `/` diagonal para la mitad inferior derecha de la matriz
        for ($c = 1; $c < $this->a; $c++)
        {
            $string='';
            // comienza desde cada celda de la última fila
            for ($i =  $this->a- 1, $j = $c; $j < $this->a && $i >= 0; $i--, $j++) {
                $string.=$matriz[$i][$j];

            }
            $vertical[]=$string;
        }

        return $vertical;
    }
}
