<?php

    class BudgetItem
    {

        public $budget_item_id;
        public $budget_item_quantity;
        public $budget_item_value_unitary;
        public $budget_item_value_discount;
        public $budget_item_value_total;
        public $product;

        public function __construct($data)
        {
            $this->budget_item_id = (int)$data->budget_item_id;
            $this->budget_item_quantity = (float)$data->budget_item_quantity;
            $this->budget_item_value_discount = (float)$data->budget_item_value_discount;
            $this->budget_item_value_unitary = (float)$data->budget_item_value_unitary;
            $this->budget_item_value_total = (float)$data->budget_item_value_total;

            $this->product = (Object)[
                "CdSigla" => $data->CdSigla,
                "IdProduto" => $data->IdProduto,
                "CdChamada" => $data->CdChamada,
                "NmProduto" => $data->NmProduto,
                "CdClassificacao" => $data->CdClassificacao,
                "CdEAN" => @$data->CdEAN ? $data->CdEAN : NULL,
                "CdCEST" => @$data->CdCEST ? $data->CdCEST : NULL,
                "IdCFOP" => $data->IdCFOP,
                "unit" => (Object)[
                    "CdSigla" => $data->CdSigla
                ]
            ];
        }

        public static function getList($params)
        {
            GLOBAL $conn, $commercial;

            $data = Model::getList($commercial, (Object)[
                "join" => 1,
                "debug" => 0,
                "tables" => [
                    "{$conn->commercial->table}.dbo.BudgetItem BI",
                    "INNER JOIN {$conn->commercial->table}.dbo.BudgetItemTrib BIT ON BIT.budget_item_id = BI.budget_item_id",
                    "INNER JOIN {$conn->dafel->table}.dbo.Produto P ON P.IdProduto = BI.product_id",
                    "INNER JOIN {$conn->dafel->table}.dbo.CodigoProduto CP ON CP.IdProduto = P.IdProduto AND CP.StCodigoPrincipal = 'S'",
                    "INNER JOIN {$conn->dafel->table}.dbo.Unidade U ON U.IdUnidade = P.IdUnidade",
                    "INNER JOIN {$conn->dafel->table}.dbo.ClassificacaoFiscal CF ON CF.IdClassificacaoFiscal = P.IdClassificacaoFiscal",
                    "LEFT JOIN {$conn->dafel->table}.dbo.CEST CEST ON CEST.IdCEST = CF.IdCEST",
                ],
                "fields" => [
                    "BI.budget_item_id",
                    "budget_item_quantity=CAST(BI.budget_item_quantity AS FLOAT)",
                    "budget_item_value_discount=CAST(BI.budget_item_value_discount AS FLOAT)",
                    "budget_item_value_unitary=CAST(BI.budget_item_value_unitary AS FLOAT)",
                    "budget_item_value_total=CAST(BI.budget_item_value_total AS FLOAT)",
                    "P.IdProduto",
                    "CP.CdChamada",
                    "CF.CdClassificacao",
                    "CEST.CdCEST",
                    "CdEAN=(SELECT TOP 1 CP2.CdChamada FROM {$conn->dafel->table}.dbo.CodigoProduto CP2 WHERE CP2.IdProduto = P.IdProduto AND CP2.IdTipoCodigoProduto = CONVERT(VARCHAR,(
                        SELECT config_value FROM {$conn->commercial->table}.dbo.Config WHERE config_category = 'product' AND config_name = 'code_ean_id'
                    )))",
                    "P.NmProduto",
                    "U.CdSigla",
                    "BIT.IdCFOP"
                ],
                "filters" => [["BI.budget_id", "i", "=", $params->budget_id]]
            ]);

            $ret = [];
            foreach($data as $item){
                $ret[] = new BudgetItem($item);
            }

            return $ret;
        }
    }

?>