<?php

namespace Ravelino\Wesender;

use Ravelino\Wesender\Exceptions\CouldNotSendNotification;
use Wesender\Exceptions\WesenderException;

class Wesender
{
    /** @var WesenderConfig */
    public $config;

    public function __construct(WesenderConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Send a WesenderMessage to the a phone number.
     *
     * @param WesenderMessage $message
     * @param string|null $to
     * @param bool $useAlphanumericSender
     *
     * @return mixed
     * @throws WesenderException
     * @throws CouldNotSendNotification
     */
    public function sendMessage(WesenderMessage $message, ?string $to)
    {
        if ($message instanceof WesenderSmsMessage) {

            return $this->sendSmsMessage($message, $to);
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }

    /**
     *
     * @param WesenderSmsMessage $message
     * @param string|null $to
     *
     * @return MessageInstance
     * @throws CouldNotSendNotification
     * @throws WesenderException
     */
    protected function sendSmsMessage(WesenderSmsMessage $message, ?string $to): MessageInstance
    {
        $post_fields = [
            "ApiKey"=>$this->config->api_key,
            "Destino"=>$to,
            "Mensagem"=> $message->content,
            "CEspeciais" => $this->config->special_characters||false
        ];
        $post_fields = json_encode($post_fields);
        $http_header = [
            "Content-Type: application/json",
            "Content-Length: " . strlen($post_fields)
        ];
        $opts = [
            CURLOPT_URL             => "https://api.wesender.co.ao/envio/apikey",
            CURLOPT_CUSTOMREQUEST   => "POST",
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTPHEADER      => $http_header,
            CURLOPT_POSTFIELDS      => $post_fields
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $opts);
        $response = curl_exec($curl);
        $response = json_decode($response);
        $err = curl_error($curl);
        curl_close($curl);
        app('log')->info(json_encode($response));

        try {
            if ($response->Message == "Remetente inválido"){
                return response()->json(['message' => 'Chave da API inválido', 'success' => false]);
            }
            else if ($response->Message== '"An error has occurred."'){
                return response()->json(['message' => 'Erro desconhecido.', 'success' => false]);
            }
            else if ($response->Message== "Saldo insuficiente para realizar envio"){
                return response()->json(['message' => 'Saldo insuficiente para realizar envio.', 'success' => false]);
            }
            else if ($response->Exito == true){
                return response()->json(['wesender'=>$response, 'message' => 'SMS enviado com sucesso.', 'success' => true]);
            }
        } catch (\Throwable $th) {
            return response()->json(['wesender'=>false, 'message' => 'Erro desconhecido.', 'success' => false]);
        }
    }



}
