<?php

    include "../../config/start.php";

    GLOBAL $get, $post;

    switch($get->action){

        case "getList":

            if(!@$post->company_id || !@$post->reference || !@$post->states){
                headerResponse((Object)[
                    "code" => 417,
                    "message" => "Parâmetro POST não encontrado"
                ]);
            }

            Json::get(Budget::getList((Object)[
                "company_id" => $post->company_id,
                "reference" => $post->reference,
                "states" => $post->states,
            ]));

        break;

        case "get":

            if(!@$post->budget_id){
                headerResponse((Object)[
                    "code" => 417,
                    "message" => "Parâmetro POST não encontrado"
                ]);
            }

            $budget = Budget::get((Object)[
                "budget_id" => $post->budget_id
            ]);

            if(!@$budget){
                headerResponse((Object)[
                    "code" => 404,
                    "message" => "Documento não encontrado"
                ]);
            }

            Json::get($budget);

        break;

        case "submit":

            if(
                !@$post->company ||
                !@$post->budget_id ||
                !@$post->client_id ||
                !@$post->company_id
            ){
                headerResponse((Object)[
                    "code" => 417,
                    "message" => "Parâmetro POST não encontrado"
                ]);
            }

            $certificate = Certificate::get((Object)[
                "company_id" => $post->company_id
            ]);

            if(!@$certificate){
                headerResponse((Object)[
                    "code" => 417,
                    "message" => "Certificado não encontrado"
                ]);
            }

            $token = Token::get((Object)[
                "company_id" => $post->company_id
            ]);

            if(!@$token){
                headerResponse((Object)[
                    "code" => 417,
                    "message" => "Token não encontrado"
                ]);
            }

            $serie = Terminal::getSerie((Object)[
                "model_id" => 65,
                "company_id" => $post->company_id
            ]);

            if(!@$serie){
                headerResponse((Object)[
                    "code" => 417,
                    "message" => "Nenhuma série ativa foi localizada para a empresa"
                ]);
            }

            $post->serie = $serie;
            $post->certificate = $certificate;
            $post->company = (Object)$post->company;

            $nfe = new NFe($post);
            var_dump($nfe->emit->enderEmit);

//            var_dump($post);
//            var_dump($certificate);
//            var_dump($token);
//            var_dump($serie);

        break;
    }

?>