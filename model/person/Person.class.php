<?php

    class Person
    {
        public $image;
        public $IdPessoa;
        public $CdChamada;
        public $NmPessoa;
        public $CdCPF_CGC;
        public $StConsumidor;

        public function __construct($data)
        {
            $this->IdPessoa = $data->IdPessoa;
            $this->CdChamada = $data->CdChamada;
            $this->TpPessoa = $data->TpPessoa;
            $this->NmPessoa = $data->NmPessoa;
            $this->StConsumidor = $data->CdChamada == "097448";
            $this->CdCPF_CGC = @$data->CdCPF_CGC ? $data->CdCPF_CGC : NULL;

            $this->image = getImage((Object)[
                "image_id" => $data->IdPessoa,
                "image_dir" => "person"
            ]);

            if(@$data->CdEndereco){
                $this->address = (Object)[
                    "CdEndereco" => $data->CdEndereco,
                    "TpLogradouro" => $data->TpLogradouro,
                    "NmLogradouro" => $data->NmLogradouro,
                    "NrLogradouro" => $data->NrLogradouro,
                    "NmBairro" => $data->NmBairro,
                    "CdIBGE" => $data->CdIBGE,
                    "NmCidade" => $data->NmCidade,
                    "IdUF" => $data->IdUF,
                    "CdCEP" => $data->CdCEP,
                    "NrInscricaoEstadual" => @$data->NrInscricaoEstadual ? $data->NrInscricaoEstadual : NULL,
                    "TpContribuicaoICMS" => @$data->TpContribuicaoICMS ? $data->TpContribuicaoICMS : NULL
                ];
            }
        }

        public static function get($params)
        {
            GLOBAL $dafel;

            $tables = ["Pessoa P(NoLock)"];
            $fields = [
                "P.IdPessoa",
                "P.CdChamada",
                "P.TpPessoa",
                "P.NmPessoa",
                "P.CdCPF_CGC"
            ];

            if(@$params->CdEndereco){
                $tables[] = "INNER JOIN PessoaEndereco PE(NoLock) ON PE.IdPessoa = P.IdPessoa AND PE.CdEndereco = {$params->CdEndereco}";
                $tables[] = "INNER JOIN Bairro B(NoLock) ON B.IdBairro = PE.IdBairro";
                $tables[] = "INNER JOIN Cidade C(NoLock) ON C.IdCidade = PE.IdCidade";
                $fields[] = "PE.CdEndereco";
                $fields[] = "PE.TpLogradouro";
                $fields[] = "PE.NmLogradouro";
                $fields[] = "PE.NrLogradouro";
                $fields[] = "B.NmBairro";
                $fields[] = "C.CdIBGE";
                $fields[] = "C.NmCidade";
                $fields[] = "PE.IdUF";
                $fields[] = "PE.CdCEP";
                $fields[] = "PE.NrInscricaoEstadual";
                $fields[] = "PE.TpContribuicaoICMS";
            }

            $data = Model::get($dafel, (Object)[
                "join" => 1,
                "tables" => $tables,
                "fields" => $fields,
                "filters" => [["P.IdPessoa", "s", "=", $params->IdPessoa]]
            ]);

            if(!@$data){
                return NULL;
            }

            return new Person($data);
        }
    }

?>