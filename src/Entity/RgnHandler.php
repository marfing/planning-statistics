<?php

namespace App\Entity;

class RgnHandler
{
    private $cliList;
    private $prefixRgnMap;
    private $cliRgnMap;
    private $wrongCli;


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

    public function getWrongCli()
    {
        return $this->wrongCli;
    }

    public function newCliRgnMap()
    {
        $tempCliList = explode(",",$this->cliList);
        $tempC60List = explode(",",$this->prefixRgnMap);
        //scandiamo lista cli
        foreach($tempCliList as $cli){
            $cli = str_replace(' ', '', $cli); //rimuoviamo eventuali spazi vuoti dal cli
//            echo("<p>Aggiunto zero a CLI: $cli</p>");
            if(strlen($cli)>3 && ctype_digit($cli) && strlen($cli)<=11){ //verifico lunghezza e natura numerica della stringa
//                echo("<p>$cli è un numero valido</p>");
                if(substr($cli,0,1)!="0"){$cli="0".$cli;}//aggiungo lo zero se manca
                $index=0;
                foreach($tempC60List as $prefix){
                    $prefix = str_replace(' ', '', $prefix); //rimuoviamo eventuali spazi vuoti dal prefix
                    if( ctype_digit($prefix) && substr($prefix,0,1)!="0"){$prefix="0".$prefix;}//aggiungo lo zero se manca
//                    echo("<p>Aggiunto zero a Prefix: $prefix</p>");
        
                    if(strlen($prefix) <= 4){ //è un prefisso valido
                        if(strlen($prefix) == 4){
                            //prendo i primi 4 caratteri del cli e li confronto
                            $cliPrefix=substr($cli,0,4);
                            if($cliPrefix == $prefix){
                                $this->cliRgnMap[] = array($cli,$prefix,str_replace(' ','',$tempC60List[$index+1].$cli));
//                                echo("<p>Prefix: $prefix - Cli: $cli - C60: " . $tempC60List[$index+1] . "</p>");
                            }
                        } else { // area code con 3 caratteri
                            $cliPrefix=substr($cli,0,3);
                            if($cliPrefix == $prefix){
                                $this->cliRgnMap[] = array($cli,$prefix,str_replace(' ','',$tempC60List[$index+1].$cli));
//                                echo("<p>Prefix: $prefix - Cli: $cli - C60: " . $tempC60List[$index+1] . "</p>");
                            }
                        }
                    }
                    $index++;
                }
            } else { $this->wrongCli[] = $cli; }
        }
        return;
    }
}
