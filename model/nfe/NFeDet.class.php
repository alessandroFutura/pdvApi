<?php

    class NFeDet
    {
        //public $mod;
        public $prod;
        public $nItem;
        public $imposto;

        public function __construct($data)
        {
            //$this->item = $data;
            //$this->mod = $data->mod;
            //$this->nItem = $key + 1;
            $this->prod = new NFeDetProd($data);
            //$this->imposto = new NFeDetImposto($data);
        }

        public function xml()
        {
            $xml = new DOMDocument();
            $xml->preserveWhiteSpace = FALSE;
            $xml->load(PATH_MODEL . "nfe/xml/nfe-det.xml");
            $xml->getElementsByTagName("det")[0]->setAttribute("nItem",$this->nItem);

            $prod = $this->prod->xml();
            $xml->getElementsByTagName("det")[0]->appendChild(
                $xml->importNode($prod->getElementsByTagName("prod")[0],TRUE)
            );

            $imposto = $this->imposto->xml();
            $xml->getElementsByTagName("det")[0]->appendChild(
                $xml->importNode($imposto->getElementsByTagName("imposto")[0],TRUE)
            );

            return $xml;
        }
    }

?>