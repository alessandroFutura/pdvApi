<?php

    class NFe
    {
        public $mod=65;
        public $type;

        public $serie;
        public $versao;
        public $tpAmb;
        public $path;

        public $nNF;
        public $CNPJ;
        public $cMun;

        public $cNF;
        public $dhEmi;
        public $chNFe;
        public $dhSaiEnt;
        public $idLote;
        public $digVal;

        public $det;
        public $ide;
        public $Signature;

        public function __construct($data)
        {
            $this->mod = 65;
            $this->type = "nfce";

            $this->serie = $data->serie->serie_id;
            $this->versao = XML_VERSION;
            $this->tpAmb = TP_AMBIENT;
            $this->tpEmis = TP_EMISSAO;
            $this->path = PATH_XML . "{$data->company->company_id}/";

            $this->nNF = $data->serie->terminal_company_number;
            $this->CNPJ = $data->company->external->NrCGC;
            $this->cMunFG = $data->company->external->CdIBGE;

            $this->cNF = $this->_cNF();
            $this->dhEmi = $this->_dhEmi();
            $this->dhSaiEnt = $this->_dhEmi();
            $this->chNFe = $this->_chNFe();
            $this->idLote = $this->_idLote();
            $this->cDV = $this->_cDV();

            $data->cNF = $this->cNF;
            $data->mod = $this->mod;
            $data->serie = $this->serie;
            $data->nNF = $this->nNF;
            $data->dhEmi = $this->dhEmi;
            $data->dhSaiEnt = $this->dhSaiEnt;
            $data->cMunFG = $this->cMun;
            $data->tpEmis = $this->tpEmis;
            $data->tpAmb = $this->tpAmb;
            $data->chNFe = $this->chNFe;
            $data->cDV = $this->cDV;

            $this->ide = new NFeIde($data);
            $this->emit = new NFeEmit($data->company);
            $this->dest = !$data->person->StConsumidor ? new NFeDest($data->person) : NULL;
            $this->total = new NFeTotal($data);

            $this->det = [];
            foreach($data->items as $key => $item){
                $item->nItem = $key+1;
                $item->mod = $this->mod;

//                if(in_array($item->csosn, ["101"])){
//                    $item->pCredSN = $data->company->company_icms_sn;
//                    $item->vCredICMSSN = round($item->pCredSN * $item->document_item_value_total / 100, 2);
//                    $this->total->vCredICMSSN += $item->vCredICMSSN;
//                }

                //$item->operation = $data->operation->operation_type_code;
                $det = new NFeDet($item);
                //$this->total->vFedTrib += $det->imposto->vFedTrib;
                //$this->total->vEstTrib += $det->imposto->vEstTrib;
                //$this->total->vMunTrib += $det->imposto->vMunTrib;
                //$this->total->vTotTrib += $det->imposto->vTotTrib;

                $this->det[] = $det;
            }

        }

        public function _cDV()
        {
            return (int)substr($this->chNFe, -1);
        }

        public function _cNF()
        {
            return rand(10000000, 99999999);
        }

        public function _dhEmi()
        {
            return str_replace(' ', 'T', date('Y-m-d H:i:sP'));
        }

        public function _chNFe()
        {
            $chNFe = sprintf(
                "%02d%02d%02d%s%02d%03d%09d%01d%08d",
                $this->cMunFG,
                substr($this->dhEmi,2,2),
                substr($this->dhEmi,5,2),
                $this->CNPJ,
                $this->mod,
                $this->serie,
                $this->nNF,
                $this->tpEmis,
                $this->cNF
            );

            $iCount = 42;
            $somaPonderada = 0;
            $multiplicadores = array(2, 3, 4, 5, 6, 7, 8, 9);

            while($iCount >= 0){
                for($mCount = 0; $mCount < count($multiplicadores) && $iCount >= 0; $mCount++){
                    $num = (int) substr($chNFe, $iCount, 1);
                    $peso = (int) $multiplicadores[$mCount];
                    $somaPonderada += $num * $peso;
                    $iCount--;
                }
            }

            $resto = $somaPonderada % 11;
            if($resto == '0' || $resto == '1'){
                $this->cDV = 0;
            } else{
                $this->cDV = 11 - $resto;
            }

            return "{$chNFe}{$this->cDV}";
        }

        public function _idLote()
        {
            return substr(str_replace(',', '', number_format(microtime(true)*1000000, 0)), 0, 15);
        }
    }

?>