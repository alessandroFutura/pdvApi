<?php

    class NFeDetProd
    {
        public $cProd;
        public $cEAN;
        public $xProd;
        public $NCM;
        public $CEST;
        public $CFOP;
        public $uCom;
        public $qCom;
        public $vUnCom;
        public $vProd;
        public $vFrete;
        public $cEANTrib;
        public $uTrib;
        public $qTrib;
        public $vUnTrib;
        public $indTot;

        public function __construct($data)
        {var_dump($data);
            $this->cProd = $data->product->CdChamada;
            $this->cEAN = $this->_cEAN($data);
            $this->xProd = $this->_xProd($data);
            $this->NCM = $data->product->CdClassificacao;
            $this->CEST = $this->_CEST($data);
            $this->CFOP = $this->_IdCFOP($data);
            $this->uCom = $data->product->CdSigla;
            $this->qCom = $data->budget_item_quantity;
            $this->vUnCom = $data->budget_item_value_unitary;
            $this->vProd = $data->budget_item_value_total;
            $this->vFrete = 0;
            $this->vOutro = 0;
            $this->cEANTrib = $this->_cEAN($data);
            $this->uTrib = $data->product->CdSigla;
            $this->qTrib = $data->budget_item_quantity;
            $this->vUnTrib = $data->budget_item_value_unitary;
            $this->vDesc = $data->budget_item_value_discount;
            $this->indTot = 1;
        }

        public function _cEAN($data)
        {
            return @$data->product->CdEAN ? $data->product->CdEAN : "SEM GTIN";
        }

        public function _CEST($data)
        {
            return @$data->product->CdCEST ? str_replace(".", "", $data->product->CdCEST) : NULL;
        }

        public function _IdCFOP($data)
        {
            return str_replace(".", "", $data->mod == 65 ? str_replace("6.", "5.", $data->product->IdCFOP) : $data->product->IdCFOP);
        }

        public function _xProd($data)
        {
            return $data->mod == 65 && TP_AMBIENT == 2 ? "NOTA FISCAL EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL" : strtoupper(removeSpecialChar($data->product->NmProduto));
        }

        public function xml()
        {
            $xml = new DOMDocument();
            $xml->preserveWhiteSpace = FALSE;
            $xml->load(PATH_MODEL . "nfe/xml/nfe-det-prod.xml");

            $xml->getElementsByTagName("cProd")[0]->nodeValue = $this->cProd;
            $xml->getElementsByTagName("cEAN")[0]->nodeValue = $this->cEAN;
            $xml->getElementsByTagName("xProd")[0]->nodeValue = $this->xProd;
            $xml->getElementsByTagName("NCM")[0]->nodeValue = str_replace(".","",$this->NCM);

            if(@$this->CEST) {
                $xml->getElementsByTagName("CEST")[0]->nodeValue = str_replace(".","",$this->CEST);
            } else {
                $node = $xml->getElementsByTagName("CEST")[0];
                $node->parentNode->removeChild($node);
            }

            $xml->getElementsByTagName("CFOP")[0]->nodeValue = $this->CFOP;
            $xml->getElementsByTagName("uCom")[0]->nodeValue = $this->uCom;
            $xml->getElementsByTagName("qCom")[0]->nodeValue = number_format($this->qCom,4,".","");
            $xml->getElementsByTagName("vUnCom")[0]->nodeValue = number_format($this->vUnCom,4,".","");
            $xml->getElementsByTagName("vProd")[0]->nodeValue = number_format($this->vProd,2,".","");

            if(@$this->vFrete) {
                $xml->getElementsByTagName("vFrete")[0]->nodeValue = number_format($this->vFrete,2,".","");
            } else {
                $node = $xml->getElementsByTagName("vFrete")[0];
                $node->parentNode->removeChild($node);
            }

            if(@$this->vOutro) {
                $xml->getElementsByTagName("vOutro")[0]->nodeValue = number_format($this->vOutro,2,".","");
            } else {
                $node = $xml->getElementsByTagName("vOutro")[0];
                $node->parentNode->removeChild($node);
            }

            $xml->getElementsByTagName("cEANTrib")[0]->nodeValue = $this->cEANTrib;
            $xml->getElementsByTagName("uTrib")[0]->nodeValue = $this->uTrib;
            $xml->getElementsByTagName("qTrib")[0]->nodeValue = number_format($this->qTrib,4,".","");
            $xml->getElementsByTagName("vUnTrib")[0]->nodeValue = number_format($this->vUnTrib,4,".","");

            if(@$this->vDesc) {
                $xml->getElementsByTagName("vDesc")[0]->nodeValue = number_format($this->vDesc,2,".","");
            } else {
                $node = $xml->getElementsByTagName("vDesc")[0];
                $node->parentNode->removeChild($node);
            }

            $xml->getElementsByTagName("indTot")[0]->nodeValue = $this->indTot;

            return $xml;
        }
    }

?>