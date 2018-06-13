<?php

namespace App\Entity;

class RgnHandler
{
    private $cliList;
    private $prefixRgnMap;
    private $cliRgnMap;


    public function getId()
    {
        return $this->id;
    }

    public function getCliList(): ?string
    {
        return $this->cliList;
    }

    public function setCliList(?string $cliList): self
    {
        $this->cliList = $cliList;

        return $this;
    }

    public function getPrefixRgnMap(): ?string
    {
        return $this->prefixRgnMap;
    }

    public function setPrefixRgnMap(?string $prefixRgnMap): self
    {
        $this->prefixRgnMap = $prefixRgnMap;

        return $this;
    }

    public function getCliRgnMap()
    {
        return $this->cliRgnMap;
    }

    public function newCliRgnMap()
    {
        $tempCliList = explode(",",$this->cliList);
        $tempC60List = explode(",",$this->prefixRgnMap);
        //scandiamo lista cli
        foreach($tempCliList as $cli){
            $cli = str_replace(' ', '', $cli); //rimuoviamo eventuali spazi vuoti
            if(strlen($cli) > 4 && (substr($cli,0,1)!="0")){
                $index=0;
                foreach($tempC60List as $prefix){
                    $prefix = str_replace(' ', '', $prefix); //rimuoviamo eventuali spazi vuoti
                    if(strlen($prefix) <= 3){ //è un prefisso
                        if(strlen($prefix) == 3){
                            //prendo i primi 3 caratteri del cli e li confronto
                            $cliPrefix=substr($cli,0,3);
                            if($cliPrefix == $prefix){
                                $this->cliRgnMap[] = array($cli,$prefix,str_replace(' ','',$tempC60List[$index+1].$cli));
//                                echo("<p>Prefix: $prefix - Cli: $cli - C60: " . $tempC60List[$index+1] . "</p>");
                            }
                        } else { // area code con 2 caratteri
                            $cliPrefix=substr($cli,0,2);
                            if($cliPrefix == $prefix){
                                $this->cliRgnMap[] = array($cli,$prefix,str_replace(' ','',$tempC60List[$index+1].$cli));
//                                echo("<p>Prefix: $prefix - Cli: $cli - C60: " . $tempC60List[$index+1] . "</p>");
                            }
                        }
                    }
                    $index++;
                }
            } //else {echo("<p>Cli non valido: $cli - scartato</p>");}
        }
        //per ogni cli cerco il prefix (
            //cerco prima corrispondenza con prefix lunghi 3
            //se non trovo passo a prefix lunghi 2
            //se trovo corrispondenza resituisco concatenamento rgn+cli        
        dump($this->cliRgnMap);
//        return $this->cliRgnMap;
            return;
    }
}
